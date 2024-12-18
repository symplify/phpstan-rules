<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoEntityMockingRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoEntityMockingRule\Source\SomeEntity;

final class MockingEntity extends TestCase
{
    public function test(): void
    {
        $someEntityMock = $this->createMock(SomeEntity::class);
    }
}
