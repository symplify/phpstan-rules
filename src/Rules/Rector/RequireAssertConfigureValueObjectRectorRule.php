<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariantWithPhpDocs;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\ArrayType;
use PHPStan\Type\TypeWithClassName;
use Webmozart\Assert\Assert;

/**
 * @see \Rector\PHPStanRules\Tests\Rule\RequireAssertConfigureValueObjectRectorRule\RequireAssertConfigureValueObjectRectorRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class RequireAssertConfigureValueObjectRectorRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method configure() with passed value object must contain assert to verify passed type';

    public function __construct(
        private readonly NodeFinder $nodeFinder
    ) {
    }

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

        if (! $classReflection->isSubclassOf('Rector\Contract\Rector\ConfigurableRectorInterface')) {
            return [];
        }

        if (! $this->hasArrayObjectTypeParam($node, $classReflection)) {
            return [];
        }

        if ($this->hasAssertAllIsAOfStaticCall($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
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

        foreach ($extendedMethodReflection->getVariants() as $parametersAcceptorWithPhpDoc) {
            if (! $parametersAcceptorWithPhpDoc instanceof FunctionVariantWithPhpDocs) {
                continue;
            }

            if ($parametersAcceptorWithPhpDoc->getParameters() === []) {
                continue;
            }

            $configurationParameterReflection = $parametersAcceptorWithPhpDoc->getParameters()[0];
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
