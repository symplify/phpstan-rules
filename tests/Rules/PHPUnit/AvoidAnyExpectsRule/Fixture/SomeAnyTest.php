<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\AvoidAnyExpectsRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SomeAnyTest extends TestCase
{
    public function test(): void
    {
        $mock = $this->createMock(\stdClass::class);

        $mock->expects($this->any())
            ->method('someMethod')
            ->willReturn('value');
    }
}
