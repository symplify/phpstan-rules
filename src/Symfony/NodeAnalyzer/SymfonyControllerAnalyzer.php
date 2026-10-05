<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer;

use Entropy\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node;
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
        SymfonyClass::CONTROLLER,
        SymfonyClass::ABSTRACT_CONTROLLER,
    ];

    public static function isControllerScope(Scope $scope): bool
    {
        if (! $scope->isInClass()) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        $found = false;
        foreach (self::CONTROLLER_TYPES as $controllerType) {
            if ($classReflection->is($controllerType)) {
                $found = true;
                break;
            }
        }

        return $found;
    }

    /**
     * @param ClassLike|ClassMethod $node
     */
    public static function hasRouteAnnotationOrAttribute(Node $node): bool
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

        if (Strings::contains($docComment->getText(), SymfonyClass::ROUTE_ANNOTATION)) {
            return true;
        }

        return Strings::contains($docComment->getText(), '@Route');
    }
}
