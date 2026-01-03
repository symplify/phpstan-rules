<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoRepositoryCallInDataFixtureRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\NoRepositoryCallInDataFixtureRule;

final class NoRepositoryCallInDataFixtureRuleTest extends RuleTestCase
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
        yield [
            __DIR__ . '/Fixture/SomeRepositoryCallInFixture.php',
            [
                [NoRepositoryCallInDataFixtureRule::ERROR_MESSAGE, 14],
                [NoRepositoryCallInDataFixtureRule::ERROR_MESSAGE, 16],
            ],
        ];

        yield [__DIR__ . '/Fixture/SkipNonFixtureClass.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoRepositoryCallInDataFixtureRule();
    }
}
