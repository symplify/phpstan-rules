<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Testing;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;

final class PHPUnitTestAnalyser
{
    private const string TEST_CASE_CLASS = 'PHPUnit\Framework\TestCase';

    public static function isTestClass(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return $classReflection->is(self::TEST_CASE_CLASS);
    }

    /**
     * @api is used
     */
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
