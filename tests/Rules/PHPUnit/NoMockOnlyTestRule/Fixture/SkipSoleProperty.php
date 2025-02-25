<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockOnlyTestRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipSoleProperty extends TestCase
{
    private array $someItems = [];
}
