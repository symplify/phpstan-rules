<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\RectorCheaperGuardsFirstRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Rector\RectorCheaperGuardsFirstRule;

final class RectorCheaperGuardsFirstRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCheapGuardFirst.php', []];

        yield [__DIR__ . '/Fixture/SkipDependentGuard.php', []];

        yield [__DIR__ . '/Fixture/SkipSideEffectAnchor.php', []];

        yield [__DIR__ . '/Fixture/SkipByRefClosureAnchor.php', []];

        yield [__DIR__ . '/Fixture/ExpensiveBeforeCheapGuard.php', [
            [
                sprintf(RectorCheaperGuardsFirstRule::ERROR_MESSAGE, 26, 20),
                26,
            ],
        ]];
    }

    protected function getRule(): Rule
    {
        return new RectorCheaperGuardsFirstRule();
    }
}
