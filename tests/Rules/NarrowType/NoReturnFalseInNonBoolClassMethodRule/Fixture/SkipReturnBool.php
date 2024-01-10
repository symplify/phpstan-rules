<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoReturnFalseInNonBoolClassMethodRule\Fixture;

final class SkipReturnBool
{
    public function run(): bool
    {
        if (mt_rand(1, 0)) {
            return true;
        }

        return false;
    }
}
