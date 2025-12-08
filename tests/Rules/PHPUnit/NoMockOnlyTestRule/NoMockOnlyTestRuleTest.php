<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockOnlyTestRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PHPUnit\NoMockOnlyTestRule;

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
        yield [__DIR__ . '/Fixture/SkipSoleProperty.php', []];
        yield [__DIR__ . '/Fixture/SkipConstraintValidatorTest.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoMockOnlyTestRule();
    }
}
