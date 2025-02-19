<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer;

use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Enum\SymfonyClass;
use Symplify\PHPStanRules\NodeAnalyzer\AttributeFinder;

final class SymfonyControllerAnalyzer
{
    /**
     * @var string[]
     */
    private const CONTROLLER_TYPES = [
        SymfonyClass::SYMFONY_CONTROLLER,
        SymfonyClass::SYMFONY_ABSTRACT_CONTROLLER,
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
        return self::hasRouteAnnotationOrAttribute($classMethod);
    }

    public static function hasRouteAnnotationOrAttribute(ClassLike | ClassMethod $node): bool
    {
        if ($node instanceof ClassMethod && ! $node->isPublic()) {
            return false;
        }

        $attributeFinder = new AttributeFinder();

        if ($attributeFinder->hasAttribute($node, SymfonyClass::ROUTE_ATTRIBUTE)) {
            return true;
        }

        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        if (str_contains($docComment->getText(), 'Symfony\Component\Routing\Annotation\Route')) {
            return true;
        }

        return \str_contains($docComment->getText(), '@Route');
    }
}
