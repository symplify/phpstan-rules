<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisOnParentMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\RequireThisOnParentMethodCallRule;

final class RequireThisOnParentMethodCallRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCallParentMethodStaticallySameMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipCallParentMethodStaticallyWhenMethodOverriden.php', []];
        yield [__DIR__ . '/Fixture/SkipDynamicStaticCallsOnClassStrings.php', []];

        yield [
            __DIR__ . '/Fixture/CallParentMethodStatically.php',
            [[RequireThisOnParentMethodCallRule::ERROR_MESSAGE, 11], [
                RequireThisOnParentMethodCallRule::ERROR_MESSAGE,
                12,
            ]],
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
        return self::getContainer()->getByType(RequireThisOnParentMethodCallRule::class);
    }
}
