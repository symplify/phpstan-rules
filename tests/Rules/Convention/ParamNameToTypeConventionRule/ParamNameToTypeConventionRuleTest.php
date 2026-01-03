<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Convention\ParamNameToTypeConventionRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Convention\ParamNameToTypeConventionRule;

final class ParamNameToTypeConventionRuleTest extends RuleTestCase
{
    /**
     * @param array<int, array<string|int>> $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        $errorMessage = sprintf(ParamNameToTypeConventionRule::ERROR_MESSAGE, 'userId', 'int');

        yield [__DIR__ . '/Fixture/SomeUntypedParam.php', [
            [$errorMessage, 7],
        ]];

        yield [__DIR__ . '/Fixture/SkipVariadic.php', []];
        yield [__DIR__ . '/Fixture/SkipAlreadyTyped.php', []];
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
        return self::getContainer()->getByType(ParamNameToTypeConventionRule::class);
    }
}
