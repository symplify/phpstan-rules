<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoAssertFuncCallInTestsRule\Fixture;

final class SkipTestOutside
{
    public function process(int $input)
    {
        assert($input === 100);
    }
}
