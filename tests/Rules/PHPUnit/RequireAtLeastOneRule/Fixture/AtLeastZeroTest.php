<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\RequireAtLeastOneRule\Fixture;

use PHPUnit\Framework\TestCase;

final class AtLeastZeroTest extends TestCase
{
    public function test(): void
    {
        $mock = $this->createMock(\stdClass::class);

        $mock->expects($this->atLeast(0))
            ->method('someMethod')
            ->willReturn('value');
    }
}
