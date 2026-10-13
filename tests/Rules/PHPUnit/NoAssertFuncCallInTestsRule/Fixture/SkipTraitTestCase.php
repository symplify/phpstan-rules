<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoAssertFuncCallInTestsRule\Fixture;

trait SkipTraitTestCase
{
    public function testMe(int $input)
    {
        assert($input === 100);
    }
}
