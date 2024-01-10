<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoInstanceOfStaticReflectionRule\Fixture;

use PHPStan\Type\ObjectType;

final class SkipSelfType extends ObjectType
{
    public function check(object $object): bool
    {
        return $object instanceof self;
    }
}
