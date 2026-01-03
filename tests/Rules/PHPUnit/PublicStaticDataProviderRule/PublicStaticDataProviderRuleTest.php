<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\PublicStaticDataProviderRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PHPUnit\PublicStaticDataProviderRule;

final class PublicStaticDataProviderRuleTest extends RuleTestCase
{
    /**
     * @param string[] $filePaths
     * @param array<int, array<string|int>> $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(array $filePaths, array $expectedErrorsWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<array<int, array<int, mixed>>, mixed>>
     */
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
