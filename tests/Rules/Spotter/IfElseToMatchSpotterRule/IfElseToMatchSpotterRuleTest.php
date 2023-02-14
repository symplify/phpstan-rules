<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Spotter\IfElseToMatchSpotterRule;

final class IfElseToMatchSpotterRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipComplexValue.php', []];
        yield [__DIR__ . '/Fixture/SkipAlmostMatching.php', []];

        yield [__DIR__ . '/Fixture/IncludeNonEmpty.php', [[IfElseToMatchSpotterRule::ERROR_MESSAGE, 13]]];
        yield [__DIR__ . '/Fixture/EmptyArrayAssign.php', [[IfElseToMatchSpotterRule::ERROR_MESSAGE, 13]]];
        yield [__DIR__ . '/Fixture/MatchingIfCandidate.php', [[IfElseToMatchSpotterRule::ERROR_MESSAGE, 11]]];
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
        return self::getContainer()->getByType(IfElseToMatchSpotterRule::class);
    }
}
