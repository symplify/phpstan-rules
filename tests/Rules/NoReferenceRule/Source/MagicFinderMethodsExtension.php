<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\Source;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\ShouldNotHappenException;

// mimics phpstan-doctrine magic finders: hasMethod() reports findBy*() as present, but getMethod() throws as there is no real reflection
final class MagicFinderMethodsExtension implements MethodsClassReflectionExtension
{
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        return $classReflection->getName() === AbstractMagicFinderRepository::class
            && str_starts_with($methodName, 'findBy');
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): ExtendedMethodReflection
    {
        throw new ShouldNotHappenException(sprintf(
            'Method %s() was not found in reflection of class %s.',
            $methodName,
            $classReflection->getName()
        ));
    }
}
