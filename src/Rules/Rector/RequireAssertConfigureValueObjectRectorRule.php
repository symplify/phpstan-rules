<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ExtendedFunctionVariant;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ArrayType;
use PHPStan\Type\TypeWithClassName;
use Webmozart\Assert\Assert;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\RequireAssertConfigureValueObjectRectorRule\RequireAssertConfigureValueObjectRectorRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class RequireAssertConfigureValueObjectRectorRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method configure() with passed value object must contain assert to verify passed type';

    /**
     * @readonly
     */
    private NodeFinder $nodeFinder;

    public function __construct(
    ) {
        $this->nodeFinder = new NodeFinder();
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->isSubclassOf('Rector\Contract\Rector\ConfigurableRectorInterface')) {
            return [];
        }

        if (! $this->hasArrayObjectTypeParam($node, $classReflection)) {
            return [];
        }

        if ($this->hasAssertAllIsAOfStaticCall($node)) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)->build()];
    }

    private function hasAssertAllIsAOfStaticCall(ClassMethod $classMethod): bool
    {
        /** @var StaticCall[] $staticCalls */
        $staticCalls = $this->nodeFinder->findInstanceOf($classMethod, StaticCall::class);

        foreach ($staticCalls as $staticCall) {
            if ($staticCall->class instanceof Name && $staticCall->class->toString() !== Assert::class) {
                continue;
            }

            if ($staticCall->name instanceof Identifier) {
                $methodName = $staticCall->name->toString();
                if (in_array($methodName, ['allIsAOf', 'allIsInstanceOf'], true)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasArrayObjectTypeParam(ClassMethod $classMethod, ClassReflection $classReflection): bool
    {
        $methodName = $classMethod->name->toString();
        if (! $classReflection->hasMethod($methodName)) {
            return false;
        }

        $extendedMethodReflection = $classReflection->getNativeMethod($methodName);
        if (! $extendedMethodReflection instanceof PhpMethodReflection) {
            return false;
        }

        foreach ($extendedMethodReflection->getVariants() as $variant) {
            if (! $variant instanceof ExtendedFunctionVariant) {
                continue;
            }

            if ($variant->getParameters() === []) {
                continue;
            }

            $configurationParameterReflection = $variant->getParameters()[0];
            $phpDocType = $configurationParameterReflection->getPhpDocType();
            if (! $phpDocType instanceof ArrayType) {
                continue;
            }

            $itemArrayType = $phpDocType->getItemType();
            if (! $itemArrayType instanceof ArrayType) {
                continue;
            }

            if (! $itemArrayType->getItemType() instanceof TypeWithClassName) {
                continue;
            }

            return true;
        }

        return false;
    }
}
