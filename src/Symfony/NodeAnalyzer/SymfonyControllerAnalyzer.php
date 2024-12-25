<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer;

use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Enum\ClassName;
use Symplify\PHPStanRules\NodeAnalyzer\AttributeFinder;

final class SymfonyControllerAnalyzer
{
    /**
     * @var string[]
     */
    private const CONTROLLER_TYPES = [
        ClassName::SYMFONY_CONTROLLER,
        ClassName::SYMFONY_ABSTRACT_CONTROLLER,
    ];

    public static function isControllerScope(Scope $scope): bool
    {
        if (! $scope->isInClass()) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        foreach (self::CONTROLLER_TYPES as $controllerType) {
            if ($classReflection->isSubclassOf($controllerType)) {
                return true;
            }
        }

        return false;
    }

    public static function isControllerActionMethod(ClassMethod $classMethod): bool
    {
        $attributeFinder = new AttributeFinder();

        if (! $classMethod->isPublic()) {
            return false;
        }

        if ($attributeFinder->hasAttribute($classMethod, ClassName::ROUTE_ATTRIBUTE)) {
            return true;
        }

        $docComment = $classMethod->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        return strpos($docComment->getText(), '@Route') !== false;
    }
}
