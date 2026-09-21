<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertyToPropertyAssignRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Complexity\NoPropertyToPropertyAssignRule;

/**
 * @extends RuleTestCase<NoPropertyToPropertyAssignRule>
 */
final class NoPropertyToPropertyAssignRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(NoPropertyToPropertyAssignRule::ERROR_MESSAGE, 'primary', 'secondary');
        yield [__DIR__ . '/Fixture/ReportPropertyAssign.php', [[$errorMessage, 17]]];

        yield [__DIR__ . '/Fixture/SkipScalarAssign.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoPropertyToPropertyAssignRule();
    }
}
