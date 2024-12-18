<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;

final class PHPUnitTestAnalyser
{
    /**
     * @var string
     */
    private const TEST_CASE_CLASS = 'PHPUnit\Framework\TestCase';

    public static function isTestClass(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return $classReflection->isSubclassOf(self::TEST_CASE_CLASS);
    }

    public static function isTestClassMethod(ClassMethod $classMethod): bool
    {
        if (! $classMethod->isPublic()) {
            return false;
        }

        if (! $classMethod->isMagic()) {
            return true;
        }

        return str_starts_with($classMethod->name->toString(), 'test');
    }
}
