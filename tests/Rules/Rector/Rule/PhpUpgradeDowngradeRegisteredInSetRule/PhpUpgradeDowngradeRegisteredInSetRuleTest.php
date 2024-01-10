<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\PhpUpgradeDowngradeRegisteredInSetRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\PHPStanRules\Rule\PhpUpgradeDowngradeRegisteredInSetRule;
use Rector\PHPStanRules\Tests\Rule\PhpUpgradeDowngradeRegisteredInSetRule\Fixture\DowngradePhp80\SomePhpFeature2Rector;
use Rector\PHPStanRules\Tests\Rule\PhpUpgradeDowngradeRegisteredInSetRule\Fixture\Php80\SomePhpFeatureRector;

final class PhpUpgradeDowngradeRegisteredInSetRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipSomePhpFeatureRector.php', []];
        yield [__DIR__ . '/Fixture/Php80/SkipConfigurableRector.php', []];

        $errorMessage = sprintf(
            PhpUpgradeDowngradeRegisteredInSetRule::ERROR_MESSAGE,
            SomePhpFeatureRector::class,
            'php80.php'
        );
        yield [__DIR__ . '/Fixture/Php80/SomePhpFeatureRector.php', [[$errorMessage, 10]]];

        $errorMessage = sprintf(
            PhpUpgradeDowngradeRegisteredInSetRule::ERROR_MESSAGE,
            SomePhpFeature2Rector::class,
            'downgrade-php80.php'
        );
        yield [__DIR__ . '/Fixture/DowngradePhp80/SomePhpFeature2Rector.php', [[$errorMessage, 10]]];
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(PhpUpgradeDowngradeRegisteredInSetRule::class);
    }
}
