<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<ClassMethod>
 * @see \Symplify\PHPStanRules\Tests\Rules\PreventParentMethodVisibilityOverrideRule\PreventParentMethodVisibilityOverrideRuleTest
 */
final class PreventParentMethodVisibilityOverrideRule implements Rule
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Change "%s()" method visibility to "%s" to respect parent method visibility.';

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
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
        if (! $scope->getClassReflection() instanceof ClassReflection) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        $parentClassNames = $classReflection->getParentClassesNames();
        if ($parentClassNames === []) {
            return [];
        }

        $methodName = (string) $node->name;
        foreach ($parentClassNames as $parentClassName) {
            if (! $this->reflectionProvider->hasClass($parentClassName)) {
                continue;
            }

            $parentClassReflection = $this->reflectionProvider->getClass($parentClassName);

            if (! $parentClassReflection->hasMethod($methodName)) {
                continue;
            }

            $parentReflectionMethod = $parentClassReflection->getMethod($methodName, $scope);
            if ($this->isClassMethodCompatibleWithParentReflectionMethod($node, $parentReflectionMethod)) {
                return [];
            }

            $methodVisibility = $this->resolveReflectionMethodVisibilityAsStrings($parentReflectionMethod);

            $errorMessage = sprintf(self::ERROR_MESSAGE, $methodName, $methodVisibility);

            return [RuleErrorBuilder::message($errorMessage)
                ->identifier(RuleIdentifier::PARENT_METHOD_VISIBILITY_OVERRIDE)
                ->build()];
        }

        return [];
    }

    private function isClassMethodCompatibleWithParentReflectionMethod(
        ClassMethod $classMethod,
        ExtendedMethodReflection $extendedMethodReflection
    ): bool {
        if ($extendedMethodReflection->isPublic() && $classMethod->isPublic()) {
            return true;
        }

        // see https://github.com/phpstan/phpstan/discussions/7456#discussioncomment-2927978
        $isProtectedMethod = ! $extendedMethodReflection->isPublic() && ! $extendedMethodReflection->isPrivate();
        if ($isProtectedMethod && $classMethod->isProtected()) {
            return true;
        }

        if (! $extendedMethodReflection->isPrivate()) {
            return false;
        }

        return $classMethod->isPrivate();
    }

    private function resolveReflectionMethodVisibilityAsStrings(
        ExtendedMethodReflection $extendedMethodReflection
    ): string {
        if ($extendedMethodReflection->isPublic()) {
            return 'public';
        }

        if ($extendedMethodReflection->isPrivate()) {
            return 'private';
        }

        return 'protected';
    }
}
