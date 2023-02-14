<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoParentMethodCallOnNoOverrideProcessRule;

/**
 * @extends RuleTestCase<NoParentMethodCallOnNoOverrideProcessRule>
 */
final class NoParentMethodCallOnNoOverrideProcessRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipParentWithArgs.php', []];
        yield [__DIR__ . '/Fixture/SkipNotCallParentMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipParentMethodCallOverride.php', []];
        yield [__DIR__ . '/Fixture/SkipParentMethodCallInsideExpression.php', []];
        yield [__DIR__ . '/Fixture/SkipParentMethodCallFromDifferentMethodName.php', []];

        yield [
            __DIR__ . '/Fixture/ParentMethodCallNoOverride.php',
            [[NoParentMethodCallOnNoOverrideProcessRule::ERROR_MESSAGE, 11]],
        ];

        yield [
            __DIR__ . '/Fixture/OverrideWithSameParamsAndArgs.php',
            [[NoParentMethodCallOnNoOverrideProcessRule::ERROR_MESSAGE, 11]],
        ];
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
        return self::getContainer()->getByType(NoParentMethodCallOnNoOverrideProcessRule::class);
    }
}
