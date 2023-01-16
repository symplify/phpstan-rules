<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Rules\Rule;
use PHPStan\Type\BooleanType;
use Symplify\PHPStanRules\Naming\BoolishNameAnalyser;
use Symplify\PHPStanRules\NodeFinder\ReturnNodeFinder;
use Symplify\PHPStanRules\ParentGuard\ParentClassMethodGuard;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule\BoolishClassMethodPrefixRuleTest
 */
final class BoolishClassMethodPrefixRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method "%s()" returns bool type, so the name should start with is/has/was...';

    public function __construct(
        private readonly BoolishNameAnalyser $boolishNameAnalyser,
        private readonly ReturnNodeFinder $returnNodeFinder,
        private readonly ParentClassMethodGuard $parentClassMethodGuard
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

        if ($this->shouldSkip($node, $scope, $classReflection)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, (string) $node->name);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function old(): bool
    {
        return $this->age > 100;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function isOld(): bool
    {
        return $this->age > 100;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkip(ClassMethod $classMethod, Scope $scope, ClassReflection $classReflection): bool
    {
        $classMethodName = $classMethod->name->toString();

        if ($this->parentClassMethodGuard->isClassMethodGuardedByParentClassMethod($classMethod, $scope)) {
            return true;
        }

        $returns = $this->returnNodeFinder->findReturnsWithValues($classMethod);
        // nothing was returned
        if ($returns === []) {
            return true;
        }

        $extendedMethodReflection = $classReflection->getNativeMethod($classMethodName);
        $returnType = ParametersAcceptorSelector::selectSingle($extendedMethodReflection->getVariants())
            ->getReturnType();

        if (! $returnType instanceof BooleanType && ! $this->areOnlyBoolReturnNodes($returns, $scope)) {
            return true;
        }

        if ($this->boolishNameAnalyser->isBoolish($classMethodName)) {
            return true;
        }

        // is required by an interface
        return $this->isMethodRequiredByParentInterface($classReflection, $classMethodName);
    }

    /**
     * @param Return_[] $returns
     */
    private function areOnlyBoolReturnNodes(array $returns, Scope $scope): bool
    {
        foreach ($returns as $return) {
            if ($return->expr === null) {
                continue;
            }

            $returnedNodeType = $scope->getType($return->expr);
            if (! $returnedNodeType instanceof BooleanType) {
                return false;
            }
        }

        return true;
    }

    private function isMethodRequiredByParentInterface(ClassReflection $classReflection, string $methodName): bool
    {
        $interfaces = $classReflection->getInterfaces();
        foreach ($interfaces as $interface) {
            if ($interface->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}
