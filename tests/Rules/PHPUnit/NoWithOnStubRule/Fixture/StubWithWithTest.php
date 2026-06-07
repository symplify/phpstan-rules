<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoWithOnStubRule\Fixture;

use PHPUnit\Framework\TestCase;

final class StubWithWithTest extends TestCase
{
    public function test(): void
    {
        $mock = $this->createMock(\stdClass::class);

        $mock->method('someMethod')
            ->with('arg')
            ->willReturn('value');
    }
}
