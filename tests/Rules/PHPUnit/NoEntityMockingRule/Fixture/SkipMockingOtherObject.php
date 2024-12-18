<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoEntityMockingRule\Fixture;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Handyman\Tests\PHPStan\Rule\NoEntityMockingRule\Source\SimpleObject;

final class SkipMockingOtherObject extends TestCase
{
    public function test(): void
    {
        $someEntityMock = $this->createMock(SimpleObject::class);
    }
}
