<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoWithOnStubRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PHPUnit\NoWithOnStubRule;

final class NoWithOnStubRuleTest extends RuleTestCase
{
    /**
     * @param array<int, array<string|int>> $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/StubWithWithTest.php', [[NoWithOnStubRule::ERROR_MESSAGE, 13]]];

        yield [__DIR__ . '/Fixture/SkipMockWithExpectsAndWithTest.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoWithOnStubRule();
    }
}
