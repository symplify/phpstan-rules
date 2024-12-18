<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoMockOnlyTestRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\Handyman\PHPStan\Rule\NoMockOnlyTestRule;

final class NoMockOnlyTestRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeTestWithOnlyMocks.php', [[NoMockOnlyTestRule::ERROR_MESSAGE, 9]]];

        yield [__DIR__ . '/Fixture/SkipTestWithClass.php', []];
        yield [__DIR__ . '/Fixture/SkipNoProperty.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoMockOnlyTestRule();
    }
}
