<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Issues;

use PHPUnit\Framework\TestCase;

final class InstantiateMaximumIgnoredErrorCountRuleTest extends TestCase
{
    public function testInstantiation(): void
    {
        $rule = new \Symplify\PHPStanRules\Rules\MaximumIgnoredErrorCountRule(10);
        $this->assertInstanceOf(\Symplify\PHPStanRules\Rules\MaximumIgnoredErrorCountRule::class, $rule);
    }
}
