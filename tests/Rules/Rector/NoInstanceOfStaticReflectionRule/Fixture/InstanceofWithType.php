<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoInstanceOfStaticReflectionRule\Fixture;

use Hoa\Math\Sampler\Random;

final class InstanceofWithType
{
    public function check(object $object): bool
    {
        if ($object instanceof Random) {
            return true;
        }

        return false;
    }
}
