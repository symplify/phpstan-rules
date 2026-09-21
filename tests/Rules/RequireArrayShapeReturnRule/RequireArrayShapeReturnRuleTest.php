<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireArrayShapeReturnRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\RequireArrayShapeReturnRule;

/**
 * @extends RuleTestCase<RequireArrayShapeReturnRule>
 */
final class RequireArrayShapeReturnRuleTest extends RuleTestCase
{
    /**
     * @param list<array{0: string, 1: int}> $expectedErrorsWithLines
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
        $errorMessage = sprintf(RequireArrayShapeReturnRule::ERROR_MESSAGE, 'run', 2);
        yield [__DIR__ . '/Fixture/ReportKeyedArrayReturn.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipShapedReturn.php', []];
        yield [__DIR__ . '/Fixture/SkipSingleValueReturn.php', []];
        yield [__DIR__ . '/Fixture/SkipPositionalArrayReturn.php', []];
    }

    protected function getRule(): Rule
    {
        return new RequireArrayShapeReturnRule();
    }
}
