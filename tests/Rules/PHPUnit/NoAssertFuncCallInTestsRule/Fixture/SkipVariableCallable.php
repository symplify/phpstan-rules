<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoAssertFuncCallInTestsRule\Fixture;

final class SkipVariableCallable
{
    public function testMe(callable $assert)
    {
        $assert(100);
    }
}
