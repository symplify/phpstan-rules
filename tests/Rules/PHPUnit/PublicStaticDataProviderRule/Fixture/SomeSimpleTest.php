<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\PublicStaticDataProviderRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SomeSimpleTest extends TestCase
{
    /**
     * @dataProvider provideData
     */
    public function testRun()
    {
    }

    protected function provideData(): array
    {
        return [1, 2, 3];
    }
}
