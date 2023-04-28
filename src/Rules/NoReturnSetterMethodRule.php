<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;
use Symplify\PHPStanRules\NodeFinder\TypeAwareNodeFinder;
use Symplify\PHPStanRules\NodeVisitor\HasScopedReturnNodeVisitor;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule\NoReturnSetterMethodRuleTest
 */
final class NoReturnSetterMethodRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Setter method cannot return anything, only set value';

    /**
     * @var string
     * @see https://regex101.com/r/IIvg8L/1
     */
    private const SETTER_START_REGEX = '#^set[A-Z]#';

    public function __construct(
        private readonly TypeAwareNodeFinder $typeAwareNodeFinder,
        private readonly bool $allowFluentSetter = false
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->isClass()) {
            return [];
        }

        $classMethodName = $node->name->toString();
        if (strpos($classMethodName, 'setUp') === 0) {
            return [];
        }

        if (! Strings::match($classMethodName, self::SETTER_START_REGEX)) {
            return [];
        }

        if (! $this->hasReturnReturnFunctionLike($node, $scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private $name;

    public function setName(string $name): int
    {
        return 1000;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private $name;

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
CODE_SAMPLE
            ),
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private $name;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setAge(int $age): int
    {
        $this->age = $age;

        return $age;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private $name;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }
}
CODE_SAMPLE
                ,
                [ 'allowFluentSetter' => true ]
            ),
        ]);
    }

    private function hasReturnReturnFunctionLike(ClassMethod $classMethod, Scope $scope): bool
    {
        $hasScopedReturnNodeVisitor = new HasScopedReturnNodeVisitor();

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($hasScopedReturnNodeVisitor);
        $nodeTraverser->traverse([$classMethod]);

        if ($hasScopedReturnNodeVisitor->hasReturn()) {
            if (! $this->allowFluentSetter) {
                return true;
            }

            $returnType = $scope->getType($hasScopedReturnNodeVisitor->getReturnExpr());
            $declaringType = new ObjectType($scope->getClassReflection()->getName());

            return $declaringType->accepts($returnType, true)->no();
        }

        $yield = $this->typeAwareNodeFinder->findFirstInstanceOf($classMethod, Yield_::class);
        return $yield instanceof Yield_;
    }
}
