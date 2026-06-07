<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\RequireAtLeastOneRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipAtLeastOneTest extends TestCase
{
    public function test(): void
    {
        $mock = $this->createMock(\stdClass::class);

        $mock->expects($this->atLeast(1))
            ->method('someMethod')
            ->willReturn('value');

        $anotherMock = $this->createMock(\stdClass::class);
        $anotherMock->expects($this->atLeast(3))
            ->method('anotherMethod')
            ->willReturn('value');
    }
}
