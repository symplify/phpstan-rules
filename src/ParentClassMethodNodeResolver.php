<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\PHPStanRules\Reflection\ReflectionParser;

final class ParentClassMethodNodeResolver
{
    /**
     * @var \Symplify\PHPStanRules\Reflection\ReflectionParser
     */
    private $reflectionParser;
    /**
     * @var \PHPStan\Reflection\ReflectionProvider
     */
    private $reflectionProvider;
    public function __construct(ReflectionParser $reflectionParser, ReflectionProvider $reflectionProvider)
    {
        $this->reflectionParser = $reflectionParser;
        $this->reflectionProvider = $reflectionProvider;
    }

    public function resolveParentClassMethod(Scope $scope, string $methodName): ?ClassMethod
    {
        $parentClassReflections = $this->getParentClassReflections($scope);

        foreach ($parentClassReflections as $parentClassReflection) {
            if (! $parentClassReflection->hasMethod($methodName)) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($parentClassReflection->getName());
            $parentMethodReflection = $classReflection->getMethod($methodName, $scope);
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
            static function (ClassReflection $classReflection) use ($mainClassReflection) : bool {
                return $classReflection !== $mainClassReflection;
            }
        );
    }
}
