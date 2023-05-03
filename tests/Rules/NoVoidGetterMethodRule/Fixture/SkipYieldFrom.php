<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoVoidGetterMethodRule\Fixture;

use Iterator;

final class SkipYieldFrom
{
    public function get(): Iterator
    {
        yield from [200];
    }
}
