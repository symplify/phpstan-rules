<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\PHPStanRules\Reflection\ReflectionParser;

final readonly class ParentClassMethodNodeResolver
{
    public function __construct(
        private ReflectionParser $reflectionParser,
        private ReflectionProvider $reflectionProvider
    ) {
    }

    public function resolveParentClassMethod(Scope $scope, string $methodName): ?ClassMethod
    {
        $parentClassReflections = $this->getParentClassReflections($scope);

        foreach ($parentClassReflections as $parentClassReflection) {
            // native only: avoid magic methods from extensions (e.g. Doctrine findOneBy*) which can be reported by hasMethod() but throw on getMethod()
            if (! $parentClassReflection->hasNativeMethod($methodName)) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($parentClassReflection->getName());
            $parentMethodReflection = $classReflection->getNativeMethod($methodName);
            return $this->reflectionParser->parseMethodReflection($parentMethodReflection);
        }

        return null;
    }

    /**
     * @return ClassReflection[]
     */
    private function getParentClassReflections(Scope $scope): array
    {
        $mainClassReflection = $scope->getClassReflection();
        if (! $mainClassReflection instanceof ClassReflection) {
            return [];
        }

        // all parent classes and interfaces
        return array_filter(
            $mainClassReflection->getAncestors(),
            static fn (ClassReflection $classReflection): bool => $classReflection !== $mainClassReflection
        );
    }
}
