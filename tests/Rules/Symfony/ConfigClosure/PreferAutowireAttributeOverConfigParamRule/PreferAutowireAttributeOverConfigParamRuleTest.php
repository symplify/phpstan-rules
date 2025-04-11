<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\PreferAutowireAttributeOverConfigParamRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\PreferAutowireAttributeOverConfigParamRule;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\PreferAutowireAttributeOverConfigParamRule\Source\SomeSetServiceWithConstructor;

final class PreferAutowireAttributeOverConfigParamRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeConfigWithInvalidSet.php', [
            [
                sprintf(PreferAutowireAttributeOverConfigParamRule::ERROR_MESSAGE, SomeSetServiceWithConstructor::class),
                12,
            ],
        ]];

        yield [__DIR__ . '/Fixture/ParameterPercentReference.php', [
            [
                sprintf(PreferAutowireAttributeOverConfigParamRule::ERROR_MESSAGE, SomeSetServiceWithConstructor::class),
                11,
            ],
        ]];

        yield [__DIR__ . '/Fixture/SkipNoParameterReference.php', []];
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(PreferAutowireAttributeOverConfigParamRule::class);
    }
}
