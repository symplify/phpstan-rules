<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\PhpUpgradeImplementsMinPhpVersionInterfaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Rector\PhpUpgradeImplementsMinPhpVersionInterfaceRule;

final class PhpUpgradeImplementsMinPhpVersionInterfaceRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipDowngradeRector.php', []];
        yield [__DIR__ . '/Fixture/SkipAlreadyImplementsMinPhpVersionRector.php', []];
        yield [__DIR__ . '/Fixture/SomePhpFeatureRector.php', [
            [
                sprintf(
                    PhpUpgradeImplementsMinPhpVersionInterfaceRule::ERROR_MESSAGE,
                    'Rector\Php80\Rector\Class_\SomePhpFeatureRector',
                ),
                7,
            ],
        ]];
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(PhpUpgradeImplementsMinPhpVersionInterfaceRule::class);
    }
}
