<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Issues;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Rules\MaximumIgnoredErrorCountRule;

final class InstantiateMaximumIgnoredErrorCountRuleTest extends TestCase
{
    public function testInstantiation(): void
    {
        $maximumIgnoredErrorCountRule = new MaximumIgnoredErrorCountRule(10);
        $this->assertInstanceOf(MaximumIgnoredErrorCountRule::class, $maximumIgnoredErrorCountRule);
    }
}
