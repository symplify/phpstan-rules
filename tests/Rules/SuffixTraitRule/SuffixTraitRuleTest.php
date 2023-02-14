<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\SuffixTraitRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\SuffixTraitRule;

final class SuffixTraitRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCorrectlyNameTrait.php', []];

        yield [__DIR__ . '/Fixture/TraitWithoutSuffix.php', [[SuffixTraitRule::ERROR_MESSAGE, 7]]];
        yield [__DIR__ . '/Fixture/SomeNotTrait.php', [[SuffixTraitRule::ERROR_MESSAGE, 7]]];
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
        return self::getContainer()->getByType(SuffixTraitRule::class);
    }
}
