<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitClassSuffixesRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Explicit\ExplicitClassSuffixesRule;

final class ExplicitClassSuffixesRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/CorrectInterface.php', []];
        yield [__DIR__ . '/Fixture/CorrectTrait.php', []];

        yield [__DIR__ . '/Fixture/WrongTraitSuffix.php', [
            [ExplicitClassSuffixesRule::TRAIT_ERROR_MESSAGE, 5],
        ]];
        yield [__DIR__ . '/Fixture/WrongInterfaceSuffix.php', [
            [ExplicitClassSuffixesRule::INTERFACE_ERROR_MESSAGE, 5],
        ]];
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
        return self::getContainer()->getByType(ExplicitClassSuffixesRule::class);
    }
}
