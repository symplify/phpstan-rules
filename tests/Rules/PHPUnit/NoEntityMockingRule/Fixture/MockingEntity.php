<?php

namespace Symplify\PHPStanRules\Tests\PHPStan\Rule\NoEntityMockingRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\PHPStan\Rule\NoEntityMockingRule\Source\SomeEntity;

final class MockingEntity extends TestCase
{
    public function test(): void
    {
        $someEntityMock = $this->createMock(SomeEntity::class);
    }
}
