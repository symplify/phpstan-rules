<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\RequireAtLeastOneRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PHPUnit\RequireAtLeastOneRule;

final class RequireAtLeastOneRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/AtLeastZeroTest.php', [[RequireAtLeastOneRule::ERROR_MESSAGE, 13]]];
        yield [__DIR__ . '/Fixture/SkipAtLeastOneTest.php', []];
    }

    protected function getRule(): Rule
    {
        return new RequireAtLeastOneRule();
    }
}
