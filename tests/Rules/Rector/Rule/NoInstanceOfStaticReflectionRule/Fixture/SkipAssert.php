<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoInstanceOfStaticReflectionRule\Fixture;

use DateTime;

final class SkipAssert
{
    public function check(object $object): bool
    {
        assert($object instanceof DateTime);
    }
}
