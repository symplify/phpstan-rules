<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoAssertFuncCallInTestsRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PHPUnit\NoAssertFuncCallInTestsRule;

final class NoAssertFuncCallInTestsRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/AssertFuncCallInsideTest.php', [[NoAssertFuncCallInTestsRule::ERROR_MESSAGE, 9]]];

        yield [__DIR__ . '/Fixture/SkipTestOutside.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoAssertFuncCallInTestsRule();
    }
}
