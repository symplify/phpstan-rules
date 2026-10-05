<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PHPUnit;

use Entropy\Utils\Regex;
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

        $matches = Regex::match($docComment->getText(), '/@dataProvider\s+(?<method_name>\w+)/');

        // reference to static call on another class
        if (! isset($matches['method_name'])) {
            return null;
        }

        return $matches['method_name'];
    }
}
