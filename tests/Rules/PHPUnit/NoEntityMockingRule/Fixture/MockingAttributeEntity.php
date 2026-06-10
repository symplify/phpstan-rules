<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoEntityMockingRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoEntityMockingRule\Source\SomeAttributeEntity;

final class MockingAttributeEntity extends TestCase
{
    public function test(): void
    {
        $someEntityMock = $this->createMock(SomeAttributeEntity::class);
    }
}
