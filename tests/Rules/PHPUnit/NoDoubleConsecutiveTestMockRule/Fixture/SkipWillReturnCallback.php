<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoDoubleConsecutiveTestMockRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipWillReturnCallback extends TestCase
{
    public function test()
    {
        $this->createMock('SomeClass')
            ->expects($this->exactly(2))
            ->method('someMethod')
            ->willReturnCallback(
                function () {
                    return 'first';
                }
            );
    }
}
