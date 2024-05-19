<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer;

use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
use Symplify\PHPStanRules\NodeAnalyzer\AttributeFinder;

final class SymfonyControllerAnalyzer
{
    /**
     * @var string
     */
    private const ROUTE_ATTRIBUTE = 'Symfony\Component\Routing\Annotation\Route';

    public static function isControllerActionMethod(ClassMethod $classMethod): bool
    {
        $attributeFinder = new AttributeFinder();

        if (! $classMethod->isPublic()) {
            return false;
        }

        if ($attributeFinder->hasAttribute($classMethod, self::ROUTE_ATTRIBUTE)) {
            return true;
        }

        $docComment = $classMethod->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        return strpos($docComment->getText(), '@Route') !== false;
    }
}
