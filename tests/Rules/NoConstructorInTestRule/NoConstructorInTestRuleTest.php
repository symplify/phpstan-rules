<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoConstructorInTestRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoConstructorInTestRule;

/**
 * @extends RuleTestCase<NoConstructorInTestRule>
 */
final class NoConstructorInTestRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/Test1/SkipTestWithoutConstructTest.php', []];
        yield [__DIR__ . '/Fixture/Test3/SkipTestWithoutParentTest.php', []];

        yield [__DIR__ . '/Fixture/Test2/SomeTest.php', [[NoConstructorInTestRule::ERROR_MESSAGE, 12]]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoConstructorInTestRule::class);
    }
}
