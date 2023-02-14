<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedPropertyFetcherRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Explicit\NoMixedPropertyFetcherRule;

final class NoMixedPropertyFetcherRuleTest extends RuleTestCase
{
    /**
     * @param mixed[]|array<int, array<int|string>> $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipDynamicNameWithKnownType.php', []];
        yield [__DIR__ . '/Fixture/SkipKnownFetcherType.php', []];

        $message = sprintf(NoMixedPropertyFetcherRule::ERROR_MESSAGE, '$unknownType');
        yield [__DIR__ . '/Fixture/DynamicName.php', [[$message, 13]]];

        $message = sprintf(NoMixedPropertyFetcherRule::ERROR_MESSAGE, '$unknownType');
        yield [__DIR__ . '/Fixture/UnknownPropertyFetcher.php', [[$message, 11]]];
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
        return self::getContainer()->getByType(NoMixedPropertyFetcherRule::class);
    }
}
