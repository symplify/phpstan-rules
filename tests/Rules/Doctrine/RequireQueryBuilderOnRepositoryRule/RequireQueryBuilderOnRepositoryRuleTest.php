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
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipCreateQueryBuilderOnRepository.php', []];

        yield [__DIR__ . '/Fixture/ReportOnEntityManager.php', [
            [RequireQueryBuilderOnRepositoryRule::ERROR_MESSAGE, 14],
        ]];
    }

    protected function getRule(): Rule
    {
        return new RequireQueryBuilderOnRepositoryRule();
    }
}
