<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Helper;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

final class NamingHelper
{
    public static function getName(Node $node): ?string
    {
        if ($node instanceof Variable && is_string($node->name)) {
            return $node->name;
        }

        if ($node instanceof Identifier || $node instanceof Name) {
            return $node->toString();
        }

        return null;
    }

    public static function isName(Node $node, string $name): bool
    {
        return self::getName($node) === $name;
    }

    /**
     * @param string[] $names
     */
    public static function isNames(Node $node, array $names): bool
    {
        foreach ($names as $name) {
            if (self::isName($node, $name)) {
                return true;
            }
        }

        return false;
    }
}
