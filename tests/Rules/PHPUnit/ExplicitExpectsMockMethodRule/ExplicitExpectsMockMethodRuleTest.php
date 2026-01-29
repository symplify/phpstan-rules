<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\ExplicitExpectsMockMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PHPUnit\ExplicitExpectsMockMethodRule;

final class ExplicitExpectsMockMethodRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/MockWithoutExpectsTest.php', [[ExplicitExpectsMockMethodRule::ERROR_MESSAGE, 12]]];

        yield [__DIR__ . '/Fixture/SkipMockWithExpectsTest.php', []];
    }

    protected function getRule(): Rule
    {
        return new ExplicitExpectsMockMethodRule();
    }
}
