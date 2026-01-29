<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\ExplicitExpectsMockMethodRule\Fixture;

use PHPUnit\Framework\TestCase;

final class MockWithoutExpectsTest extends TestCase
{
    public function test(): void
    {
        $mock = $this->createMock(\stdClass::class);
        $mock->method('someMethod')->willReturn('value');
    }
}
