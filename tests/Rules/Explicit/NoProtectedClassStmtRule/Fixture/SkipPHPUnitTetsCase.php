<?php

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoProtectedClassStmtRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipPHPUnitTetsCase extends TestCase
{
    protected function setUp(): void
    {
        $value = 100;
    }
}
