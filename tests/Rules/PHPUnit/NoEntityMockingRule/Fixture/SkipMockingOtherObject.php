<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoEntityMockingRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoEntityMockingRule\Source\SimpleObject;

final class SkipMockingOtherObject extends TestCase
{
    public function test(): void
    {
        $someEntityMock = $this->createMock(SimpleObject::class);
    }
}
