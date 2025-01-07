<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitClassPrefixSuffixRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Explicit\ExplicitClassPrefixSuffixRule;

final class ExplicitClassPrefixSuffixRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/CorrectInterface.php', []];
        yield [__DIR__ . '/Fixture/CorrectTrait.php', []];

        yield [__DIR__ . '/Fixture/AbstractCorrectClass.php', []];
        yield [__DIR__ . '/Fixture/AbstractIncorrectClass.php', [
            [ExplicitClassPrefixSuffixRule::ABSTRACT_ERROR_MESSAGE, 5],
        ]];

        yield [__DIR__ . '/Fixture/WrongTraitSuffix.php', [
            [ExplicitClassPrefixSuffixRule::TRAIT_ERROR_MESSAGE, 5],
        ]];

        yield [__DIR__ . '/Fixture/WrongSuffixTrait.php', [
            [ExplicitClassPrefixSuffixRule::TRAIT_ERROR_MESSAGE, 5],
        ]];

        yield [__DIR__ . '/Fixture/WrongInterfaceSuffix.php', [
            [ExplicitClassPrefixSuffixRule::INTERFACE_ERROR_MESSAGE, 5],
        ]];

        yield [__DIR__ . '/Fixture/IncorrectClassInterface.php', [
            [ExplicitClassPrefixSuffixRule::INTERFACE_ERROR_MESSAGE, 5],
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
        return self::getContainer()->getByType(ExplicitClassPrefixSuffixRule::class);
    }
}
