<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoReturnSetterMethodRule;

final class NoReturnSetterMethodRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeSetterClass.php', [[NoReturnSetterMethodRule::ERROR_MESSAGE, 9]]];

        yield [__DIR__ . '/Fixture/SkipRoute.php', []];
        yield [__DIR__ . '/Fixture/SkipEmptyReturn.php', []];
        yield [__DIR__ . '/Fixture/SkipVoidSetter.php', []];
        yield [__DIR__ . '/Fixture/SkipSetUp.php', []];
        yield [__DIR__ . '/Fixture/SkipArrayFilter.php', []];
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
        return self::getContainer()->getByType(NoReturnSetterMethodRule::class);
    }
}
