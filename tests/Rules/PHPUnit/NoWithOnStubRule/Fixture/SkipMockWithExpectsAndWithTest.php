<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoWithOnStubRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipMockWithExpectsAndWithTest extends TestCase
{
    public function test(): void
    {
        $mock = $this->createMock(\stdClass::class);

        $mock->expects($this->once())
            ->method('someMethod')
            ->with('arg')
            ->willReturn('value');
    }
}
