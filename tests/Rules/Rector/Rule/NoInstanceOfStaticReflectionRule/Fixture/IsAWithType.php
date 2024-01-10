<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoInstanceOfStaticReflectionRule\Fixture;

use Hoa\Math\Sampler\Random;

final class IsAWithType
{
    public function check(object $object): bool
    {
        return is_a($object, Random::class);
    }
}
