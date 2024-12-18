<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\PublicStaticDataProviderRule\Fixture;

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
        return [1, 2, 3];
    }
}
