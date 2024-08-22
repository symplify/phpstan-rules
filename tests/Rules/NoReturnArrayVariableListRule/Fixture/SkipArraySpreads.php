<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnArrayVariableListRule\Fixture;

final class SkipArraySpreads
{
    public function run($value)
    {
        $a = [1, 2, 3];
        $b = [4, 5, 6];
        return [...$a, ...$b];
    }
}

