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
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\NodeFinder\TypeAwareNodeFinder;
use Symplify\PHPStanRules\NodeVisitor\HasScopedReturnNodeVisitor;

/**
 * @implements Rule<ClassMethod>
 * @see \Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule\NoReturnSetterMethodRuleTest
 */
final class NoReturnSetterMethodRule implements Rule
{
    /**
     * @readonly
     */
    private TypeAwareNodeFinder $typeAwareNodeFinder;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Setter method cannot return anything, only set value';

    /**
     * @see https://regex101.com/r/IIvg8L/1
     * @var string
     */
    private const SETTER_START_REGEX = '#^set[A-Z]#';

    public function __construct(TypeAwareNodeFinder $typeAwareNodeFinder)
    {
        $this->typeAwareNodeFinder = $typeAwareNodeFinder;
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
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // possibly some important logic
        if ($node->attrGroups !== []) {
            return [];
        }

        if (! $this->isInsideClassReflection($scope)) {
            return [];
        }

        $classMethodName = $node->name->toString();
        if ($classMethodName === 'setUp') {
            return [];
        }

        if (! Strings::match($classMethodName, self::SETTER_START_REGEX)) {
            return [];
        }

        if (! $this->hasReturnReturnFunctionLike($node)) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::NO_RETURN_SETTER_METHOD)
            ->build()];
    }

    private function hasReturnReturnFunctionLike(ClassMethod $classMethod): bool
    {
        $hasScopedReturnNodeVisitor = new HasScopedReturnNodeVisitor();

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($hasScopedReturnNodeVisitor);
        $nodeTraverser->traverse([$classMethod]);

        if ($hasScopedReturnNodeVisitor->hasReturn()) {
            return true;
        }

        $yield = $this->typeAwareNodeFinder->findFirstInstanceOf($classMethod, Yield_::class);
        return $yield instanceof Yield_;
    }

    private function isInsideClassReflection(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return $classReflection->isClass();
    }
}
