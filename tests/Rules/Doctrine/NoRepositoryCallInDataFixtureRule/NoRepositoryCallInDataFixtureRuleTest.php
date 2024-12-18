<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoRepositoryCallInDataFixtureRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\Handyman\PHPStan\Rule\NoRepositoryCallInDataFixtureRule;

final class NoRepositoryCallInDataFixtureRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

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
