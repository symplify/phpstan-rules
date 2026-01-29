<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\ExplicitExpectsMockMethodRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipMockWithExpectsTest extends TestCase
{
    public function test(): void
    {
        $mock = $this->createMock(\stdClass::class);

        $mock->expects($this->atLeastOnce())
            ->method('someMethod')
            ->willReturn('value');
    }
}
