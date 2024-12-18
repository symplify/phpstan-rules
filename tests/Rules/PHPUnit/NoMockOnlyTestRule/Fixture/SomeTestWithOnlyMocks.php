<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockOnlyTestRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockOnlyTestRule\Source\FirstClass;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockOnlyTestRule\Source\SecondClass;

final class SomeTestWithOnlyMocks extends TestCase
{
    private \PHPUnit\Framework\MockObject\MockObject $someMock;

    private \PHPUnit\Framework\MockObject\MockObject $anotherMock;

    protected function setUp(): void
    {
        $this->someMock = $this->createMock(FirstClass::class);

        $this->anotherMock = $this->createMock(SecondClass::class);
    }
}
