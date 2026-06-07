<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\While_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ReflectionProvider;
use Symfony\Component\HttpKernel\Kernel;
use Webmozart\Assert\Assert;

/**
 * Collect instance of `new`,
 * which is followed by a method calls on same object
 *
 * Goal is to find objects, that are created with same set of setters,
 * then pass values via constructor instead.
 *
 * @implements Collector<Node, array<array{variableName: string, className: string, setterNames: string[]}>>
 */
final readonly class NewWithFollowingSettersCollector implements Collector
{
    public const string SETTER_NAMES = 'setterNames';

    private const string VARIABLE_NAME = 'variableName';

    /**
     * @var string[]
     */
    private const array EXCLUDED_CLASSES = [Kernel::class];

    public function __construct(
        private ReflectionProvider $reflectionProvider,
        private bool $isEnabled
    ) {
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @return array<array{variableName: string, className: string, setterNames: string[]}>|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        // enable with "ctor: true" parameter
        if (! $this->isEnabled) {
            return null;
        }

        // basically all nodes that have ->stmts inside
        if (! $node instanceof ClassMethod && ! $node instanceof Function_ && ! $node instanceof If_ && ! $node instanceof ElseIf_ && ! $node instanceof While_ && ! $node instanceof Foreach_ && ! $node instanceof For_) {
            return null;
        }

        $newInstancesMetadata = [];
        $isFlowBrokenInMiddle = false;

        foreach ((array) $node->stmts as $stmt) {
            // skip iterating if there is a return expression
            if ($newInstancesMetadata !== [] && $this->isNodeBreakingFlow($stmt)) {
                $isFlowBrokenInMiddle = true;
                continue;
            }

            if (! $stmt instanceof Expression) {
                continue;
            }

            if ($stmt->expr instanceof Assign) {
                $newInstanceMetadata = $this->matchAssignNewObjectToVariable($stmt);
                if ($newInstanceMetadata !== null) {
                    $newInstancesMetadata[] = $newInstanceMetadata;
                }
            }

            // waiting for first item, before we check for method calls
            if ($newInstancesMetadata === []) {
                continue;
            }

            if ($stmt->expr instanceof MethodCall) {
                $methodCall = $stmt->expr;

                if ($methodCall->var instanceof Variable && $methodCall->name instanceof Identifier) {
                    foreach ($newInstancesMetadata as $key => $newInstanceMetadata) {
                        if ($newInstanceMetadata[self::VARIABLE_NAME] === $methodCall->var->name) {
                            // record the method call here
                            $setterMethodName = $methodCall->name->toString();

                            if (! $this->isSetterName($setterMethodName)) {
                                continue;
                            }

                            // skip no-arg setters, the value cannot be moved to the constructor
                            if ($methodCall->getArgs() === []) {
                                continue;
                            }

                            $newInstancesMetadata[$key][self::SETTER_NAMES][] = $setterMethodName;
                        }
                    }
                }
            }
        }

        if ($newInstancesMetadata === [] || $isFlowBrokenInMiddle) {
            return null;
        }

        return $newInstancesMetadata;
    }

    /**
     * @param class-string $className
     */
    private function shouldSkipClass(string $className): bool
    {
        // must be existing class
        if (! $this->reflectionProvider->hasClass($className)) {
            return true;
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        if ($classReflection->getFileName() === null) {
            return true;
        }

        // skip excluded classes
        foreach (self::EXCLUDED_CLASSES as $excludedClass) {
            if ($classReflection->is($excludedClass)) {
                return true;
            }
        }

        // skip vendor classes
        if (str_contains($classReflection->getFileName(), 'vendor')) {
            return true;
        }

        // skip Doctrine entities
        $fileContents = file_get_contents($classReflection->getFileName());
        Assert::string($fileContents);

        return str_contains($fileContents, '@ORM\Entity') || str_starts_with($fileContents, '#[Entity]');
    }

    /**
     * @return array{variableName: string, className: string, setterNames: string[]}|null
     */
    private function matchAssignNewObjectToVariable(Expression $expression): ?array
    {
        if (! $expression->expr instanceof Assign) {
            return null;
        }

        $assign = $expression->expr;
        if (! $assign->expr instanceof New_) {
            return null;
        }

        $new = $assign->expr;
        if (! $new->class instanceof Name) {
            return null;
        }

        $assignedVariable = $assign->var;
        if (! $assignedVariable instanceof Variable) {
            return null;
        }

        if (! is_string($assignedVariable->name)) {
            return null;
        }

        $variableName = $assignedVariable->name;

        /** @var class-string $className */
        $className = $new->class->toString();
        if ($this->shouldSkipClass($className)) {
            return null;
        }

        return [
            self::VARIABLE_NAME => $variableName,
            'className' => $className,
            self::SETTER_NAMES => [],
        ];
    }

    private function isNodeBreakingFlow(Node $node): bool
    {
        $nodeFinder = new NodeFinder();

        return (bool) $nodeFinder->find($node, function (Node $node): bool {
            if ($node instanceof Return_) {
                return true;
            }

            return $node instanceof Throw_;
        });
    }

    private function isSetterName(string $setterMethodName): bool
    {
        if (str_starts_with($setterMethodName, 'add')) {
            return true;
        }

        return str_starts_with($setterMethodName, 'set');
    }
}
