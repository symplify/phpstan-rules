<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\SwitchToMatchSpotterRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Spotter\SwitchToMatchSpotterRule;

final class SwitchToMatchSpotterRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNoDefault.php', []];
        yield [__DIR__ . '/Fixture/SimpleSwitch.php', [[SwitchToMatchSpotterRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/ReturnAndException.php', [[SwitchToMatchSpotterRule::ERROR_MESSAGE, 11]]];
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
        return self::getContainer()->getByType(SwitchToMatchSpotterRule::class);
    }
}
