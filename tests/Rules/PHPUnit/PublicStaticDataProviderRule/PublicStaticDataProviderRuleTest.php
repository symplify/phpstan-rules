<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\PublicStaticDataProviderRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\Handyman\PHPStan\Rule\PublicStaticDataProviderRule;

final class PublicStaticDataProviderRuleTest extends RuleTestCase
{
    /**
     * @param string[] $filePaths
     */
    #[DataProvider('provideData')]
    public function testRule(array $filePaths, array $expectedErrorsWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SomeSimpleTest.php'], [
            [sprintf(PublicStaticDataProviderRule::STATIC_ERROR_MESSAGE, 'provideData'), 16],
            [sprintf(PublicStaticDataProviderRule::PUBLIC_ERROR_MESSAGE, 'provideData'), 16],
        ]];

        yield [[__DIR__ . '/Fixture/SkipStaticTest.php'], []];
    }

    protected function getRule(): Rule
    {
        return new PublicStaticDataProviderRule();
    }
}
