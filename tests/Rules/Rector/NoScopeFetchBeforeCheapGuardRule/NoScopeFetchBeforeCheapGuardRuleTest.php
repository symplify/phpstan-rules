<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoScopeFetchBeforeCheapGuardRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Rector\NoScopeFetchBeforeCheapGuardRule;

final class NoScopeFetchBeforeCheapGuardRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipGuardBeforeScopeFetch.php', []];

        yield [__DIR__ . '/Fixture/ScopeFetchBeforeGuard.php', [
            [
                NoScopeFetchBeforeCheapGuardRule::ERROR_MESSAGE,
                15,
            ],
        ]];
    }

    protected function getRule(): Rule
    {
        return new NoScopeFetchBeforeCheapGuardRule();
    }
}
