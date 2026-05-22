<?php

namespace Symplify\PHPStanRules\Reflection;

use PHPStan\Reflection\ClassReflection;
use ReflectionMethod;

final class InvokeClassMethodResolver
{
    public static function resolve(ClassReflection $controllerClassReflection): ?\ReflectionMethod
    {
        if (! $controllerClassReflection->hasMethod('__invoke')) {
            return null;
        }

        $nativeReflectionClass = $controllerClassReflection->getNativeReflection();
        return $nativeReflectionClass->getMethod('__invoke');
    }
}
