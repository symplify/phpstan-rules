<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoInstanceOfStaticReflectionRule\Fixture;

use PHPStan\Type\StringType;

final class SkipPHPStanType
{
    public function find(object $node): bool
    {
        return $node instanceof StringType;
    }
}
