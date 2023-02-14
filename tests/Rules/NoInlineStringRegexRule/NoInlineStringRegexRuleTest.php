<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoInlineStringRegexRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoInlineStringRegexRule;

final class NoInlineStringRegexRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/InlineMatchRegex.php', [[NoInlineStringRegexRule::ERROR_MESSAGE, 11]]];
        yield [
            __DIR__ . '/Fixture/NetteUtilsStringsInlineMatchRegex.php',
            [[NoInlineStringRegexRule::ERROR_MESSAGE, 13]],
        ];

        yield [__DIR__ . '/Fixture/SkipVariable.php', []];
        yield [__DIR__ . '/Fixture/SkipSingleLetter.php', []];
        yield [__DIR__ . '/Fixture/SkipConstRegex.php', []];
        yield [__DIR__ . '/Fixture/SkipNetteUtilsStringsConstRegex.php', []];
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
        return self::getContainer()->getByType(NoInlineStringRegexRule::class);
    }
}
