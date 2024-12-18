<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan;

use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;

final class DataProviderMethodResolver
{
    public static function match(ClassMethod $classMethod): ?string
    {
        $docComment = $classMethod->getDocComment();
        if (! $docComment instanceof Doc) {
            return null;
        }

        if (! str_contains($docComment->getText(), '@dataProvider')) {
            return null;
        }

        preg_match('/@dataProvider\s+(?<method_name>\w+)/', $docComment->getText(), $matches);

        // reference to static call on another class
        if (! isset($matches['method_name'])) {
            return null;
        }

        return $matches['method_name'];
    }
}
