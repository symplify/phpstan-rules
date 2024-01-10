<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoInstanceOfStaticReflectionRule\Fixture;

use ReflectionClass;

final class SkipReflection
{
    public function find(object $node): bool
    {
        if ($node instanceof ReflectionClass) {
            return true;
        }

        return false;
    }
}
