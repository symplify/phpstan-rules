<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoJustPropertyAssignRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Complexity\NoJustPropertyAssignRule;

final class NoJustPropertyAssignRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ServiceAssign.php', [
            [NoJustPropertyAssignRule::ERROR_MESSAGE, 20],
        ]];
        yield [__DIR__ . '/Fixture/WithEqualTypeVarDoc.php', [
            [NoJustPropertyAssignRule::ERROR_MESSAGE, 18],
        ]];

        yield [__DIR__ . '/Fixture/SkipScalarAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipSpecificVarDoc.php', []];
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
        return self::getContainer()->getByType(NoJustPropertyAssignRule::class);
    }
}
