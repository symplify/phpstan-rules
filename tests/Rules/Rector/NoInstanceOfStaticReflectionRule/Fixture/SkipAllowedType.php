<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoInstanceOfStaticReflectionRule\Fixture;

final class SkipAllowedType
{
    public function check(object $object): bool
    {
        return is_a($object, \PhpParser\Node::class);
    }
}
