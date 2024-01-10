<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoInstanceOfStaticReflectionRule\Fixture;

use PhpParser\Node;

final class SkipIsAGenericClassString
{
    /**
     * @template T of Node
     * @param class-string<T> $type
     * @return T|null
     */
    public function findParentType(Node $parent, string $type)
    {
        do {
            if (is_a($parent, $type, true)) {
                return $parent;
            }
        } while ($parent = $parent->getAttribute('parent_node'));

        return null;
    }
}
