<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoInstanceOfStaticReflectionRule\Fixture;

use PHPStan\Type\ObjectType;

final class SkipSelfType extends ObjectType
{
    public function check(object $object): bool
    {
        return $object instanceof self;
    }
}
