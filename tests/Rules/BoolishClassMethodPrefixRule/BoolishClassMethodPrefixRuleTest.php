<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\BoolishClassMethodPrefixRule;

final class BoolishClassMethodPrefixRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipParentContract.php', []];
        yield [__DIR__ . '/Fixture/SkipClassWithEmptyReturn.php', []];
        yield [__DIR__ . '/Fixture/SkipClassThatImplementsInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipRequiredByInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipNestedCallback.php', []];

        $firstErrorMessage = sprintf(BoolishClassMethodPrefixRule::ERROR_MESSAGE, 'honesty');
        $secondErrorMessage = sprintf(BoolishClassMethodPrefixRule::ERROR_MESSAGE, 'thatWasGreat');

        yield [
            __DIR__ . '/Fixture/ClassWithBoolishMethods.php',
            [[$firstErrorMessage, 9], [$secondErrorMessage, 14]],
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
        return self::getContainer()->getByType(BoolishClassMethodPrefixRule::class);
    }
}
