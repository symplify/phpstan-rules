<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\PublicStaticDataProviderRule\Fixture;

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
        return [];
    }
}
