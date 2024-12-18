<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\PublicStaticDataProviderRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipStaticTest extends TestCase
{
    /**
     * @dataProvider provideData
     */
    public function testRun()
    {
    }

    public static function provideData(): array
    {
        return [];
    }
}
