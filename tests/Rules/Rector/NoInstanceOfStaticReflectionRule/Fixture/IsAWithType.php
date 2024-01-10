<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoInstanceOfStaticReflectionRule\Fixture;

use Hoa\Math\Sampler\Random;

final class IsAWithType
{
    public function check(object $object): bool
    {
        return is_a($object, Random::class);
    }
}
