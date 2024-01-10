<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoInstanceOfStaticReflectionRule\Fixture;

final class SkipAllowedType
{
    public function check(object $object): bool
    {
        return is_a($object, \PhpParser\Node::class);
    }
}
