<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoInstanceOfStaticReflectionRule\Fixture;

final class SkipGenericNodeType
{
    /**
     * @template T of \PhpParser\Node
     * @param class-string<T> $type
     */
    public function find(object $node, string $type): bool
    {
        if (is_a($node, $type, true)) {
            return true;
        }

        return true;
    }
}
