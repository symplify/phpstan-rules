<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeResolver;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

final class ClassMethodReturnTypeResolver
{
    public function resolve(ClassMethod $classMethod, Scope $scope): Type
    {
        $methodName = $classMethod->name->toString();

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return new MixedType();
        }

        $extendedMethodReflection = $classReflection->getMethod($methodName, $scope);

        $parametersAcceptorWithPhpDocs = ParametersAcceptorSelector::selectSingle($extendedMethodReflection->getVariants());
        if (! $parametersAcceptorWithPhpDocs instanceof FunctionVariant) {
            return new MixedType();
        }

        return $parametersAcceptorWithPhpDocs->getReturnType();
    }
}
