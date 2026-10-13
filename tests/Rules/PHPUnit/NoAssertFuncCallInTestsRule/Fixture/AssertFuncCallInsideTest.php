<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoAssertFuncCallInTestsRule\Fixture;

use PHPUnit\Framework\TestCase;

final class AssertFuncCallInsideTest extends TestCase
{
    public function testMe(int $input)
    {
        assert($input === 100);
    }
}
