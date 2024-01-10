<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoInstanceOfStaticReflectionRule\Fixture;

use DateTimeInterface;

final class SkipDateTime
{
    public function find(object $node): bool
    {
        return is_a($node, DateTimeInterface::class, true);
    }
}
