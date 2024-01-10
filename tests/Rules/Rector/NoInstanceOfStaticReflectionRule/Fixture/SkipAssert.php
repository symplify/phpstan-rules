<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoInstanceOfStaticReflectionRule\Fixture;

use DateTime;

final class SkipAssert
{
    public function check(object $object): bool
    {
        assert($object instanceof DateTime);
    }
}
