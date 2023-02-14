<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoNestedFuncCallRule;

/**
 * @extends RuleTestCase<NoNestedFuncCallRule>
 */
final class NoNestedFuncCallRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/NestedYourself.php', [[NoNestedFuncCallRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/NestedFuncCall.php', [[NoNestedFuncCallRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipArrowFunction.php', []];
        yield [__DIR__ . '/Fixture/SkipNonNested.php', []];
        yield [__DIR__ . '/Fixture/SkipCount.php', []];
        yield [__DIR__ . '/Fixture/SkipAssert.php', []];
        yield [__DIR__ . '/Fixture/SkipUsort.php', []];
        yield [__DIR__ . '/Fixture/SkipNestedArrayFilter.php', []];
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
        return self::getContainer()->getByType(NoNestedFuncCallRule::class);
    }
}
