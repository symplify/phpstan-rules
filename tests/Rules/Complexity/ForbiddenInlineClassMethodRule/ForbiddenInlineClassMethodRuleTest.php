<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenInlineClassMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Complexity\ForbiddenInlineClassMethodRule;

final class ForbiddenInlineClassMethodRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<string[]|array<int, array<int[]|string[]>>>
     */
    public static function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/SomeClassWithInlinedMethod.php', [
                [sprintf(ForbiddenInlineClassMethodRule::ERROR_MESSAGE, 'away'), 14],
            ], ];

        yield [__DIR__ . '/Fixture/SkipUsedTwice.php', []];
        yield [__DIR__ . '/Fixture/SkipNoMethodCall.php', []];
        yield [__DIR__ . '/Fixture/SkipMultipleLines.php', []];
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
        return self::getContainer()->getByType(ForbiddenInlineClassMethodRule::class);
    }
}
