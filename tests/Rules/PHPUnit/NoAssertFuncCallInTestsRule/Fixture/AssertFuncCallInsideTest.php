<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoAssertFuncCallInTestsRule\Fixture;

final class AssertFuncCallInsideTest
{
    public function testMe(int $input)
    {
        assert($input === 100);
    }
}
