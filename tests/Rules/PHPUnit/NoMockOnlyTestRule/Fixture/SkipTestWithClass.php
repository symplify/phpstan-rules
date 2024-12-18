<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockOnlyTestRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockOnlyTestRule\Source\FirstClass;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockOnlyTestRule\Source\SecondClass;

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
