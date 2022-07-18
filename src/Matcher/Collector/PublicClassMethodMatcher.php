<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Matcher\Collector;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ClassReflection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class PublicClassMethodMatcher
{
    /**
     * @var array<class-string>
     */
    private const SKIPPED_TYPES = [TestCase::class, ContainerConfigurator::class];

    public function shouldSkipClassReflection(ClassReflection $classReflection): bool
    {
        // skip interface as required, traits as unable to detect for sure
        if (! $classReflection->isClass()) {
            return true;
        }

        foreach (self::SKIPPED_TYPES as $skippedType) {
            if ($classReflection->isSubclassOf($skippedType)) {
                return true;
            }
        }

        return false;
    }

    public function isUsedByParentClassOrInterface(ClassReflection $classReflection, string $methodName): bool
    {
        // is this method required by parent contract? skip it
        foreach ($classReflection->getInterfaces() as $parentInterfaceReflection) {
            if ($parentInterfaceReflection->hasMethod($methodName)) {
                return true;
            }
        }

        foreach ($classReflection->getParents() as $parentClassReflection) {
            if ($parentClassReflection->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }

    public function shouldSkipClassMethod(ClassMethod $classMethod): bool
    {
        if ($classMethod->isMagic()) {
            return true;
        }

        if ($classMethod->isStatic()) {
            return true;
        }

        // skip attributes
        if ($classMethod->attrGroups !== []) {
            return true;
        }

        return ! $classMethod->isPublic();
    }
}