<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoMockOnlyTestRule\Fixture;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Handyman\Tests\PHPStan\Rule\NoMockOnlyTestRule\Source\FirstClass;
use TomasVotruba\Handyman\Tests\PHPStan\Rule\NoMockOnlyTestRule\Source\SecondClass;

final class SkipTestWithClass extends TestCase
{
    private FirstClass $firstClass;

    private \PHPUnit\Framework\MockObject\MockObject $anotherMock;

    protected function setUp(): void
    {
        $this->firstClass = new FirstClass();

        $this->anotherMock = $this->createMock(SecondClass::class);
    }
}
