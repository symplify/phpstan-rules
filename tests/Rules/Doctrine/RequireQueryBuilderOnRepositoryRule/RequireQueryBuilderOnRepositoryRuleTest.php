<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule;

final class RequireQueryBuilderOnRepositoryRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCreateQueryBuilderOnRepository.php', []];
        yield [__DIR__ . '/Fixture/SkipDocumentRepository.php', []];
        yield [__DIR__ . '/Fixture/SkipConnection.php', []];
        yield [__DIR__ . '/Fixture/SkipNonRepositoryClass.php', []];

        // builders that cannot be swapped for $this->createQueryBuilder()
        yield [__DIR__ . '/Fixture/SkipUpdateBuilder.php', []];
        yield [__DIR__ . '/Fixture/SkipDeleteBuilder.php', []];
        yield [__DIR__ . '/Fixture/SkipCrossEntityFrom.php', []];

        yield [__DIR__ . '/Fixture/ReportOnEntityManager.php', [
            [RequireQueryBuilderOnRepositoryRule::ERROR_MESSAGE, 14],
        ]];

        yield [__DIR__ . '/Fixture/ReportOnDocumentManager.php', [
            [RequireQueryBuilderOnRepositoryRule::ERROR_MESSAGE, 14],
        ]];

        yield [__DIR__ . '/Fixture/ReportOnSelfEntityFrom.php', [
            [RequireQueryBuilderOnRepositoryRule::ERROR_MESSAGE, 18],
            [RequireQueryBuilderOnRepositoryRule::ERROR_MESSAGE, 25],
        ]];

        yield [__DIR__ . '/Fixture/MixedBuildersInOneMethod.php', [
            [RequireQueryBuilderOnRepositoryRule::ERROR_MESSAGE, 19],
        ]];
    }

    protected function getRule(): Rule
    {
        return new RequireQueryBuilderOnRepositoryRule();
    }
}
