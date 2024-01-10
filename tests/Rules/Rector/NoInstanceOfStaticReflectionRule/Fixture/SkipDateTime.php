<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoInstanceOfStaticReflectionRule\Fixture;

use DateTimeInterface;

final class SkipDateTime
{
    public function find(object $node): bool
    {
        return is_a($node, DateTimeInterface::class, true);
    }
}
