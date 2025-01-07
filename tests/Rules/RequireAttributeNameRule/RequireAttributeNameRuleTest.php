<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireAttributeNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\RequireAttributeNameRule;

final class RequireAttributeNameRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/MissingName.php', [[RequireAttributeNameRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipCorrectName.php', []];
        yield [__DIR__ . '/Fixture/SkipDefaultName.php', []];
        yield [__DIR__ . '/Fixture/SkipPHPUnitAttributes.php', []];
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
        return self::getContainer()->getByType(RequireAttributeNameRule::class);
    }
}
