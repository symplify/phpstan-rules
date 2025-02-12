<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockObjectAndRealObjectPropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PHPUnit\NoMockObjectAndRealObjectPropertyRule;

final class NoMockObjectAndRealObjectPropertyRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeTestWithMockedProperties.php', [[NoMockObjectAndRealObjectPropertyRule::ERROR_MESSAGE, 10]]];
        yield [__DIR__ . '/Fixture/IntersectionMockedProperties.php', [[NoMockObjectAndRealObjectPropertyRule::ERROR_MESSAGE, 10]]];

        yield [__DIR__ . '/Fixture/SkipOneOrTheOther.php', []];
        yield [__DIR__ . '/Fixture/SkipNullableObject.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoMockObjectAndRealObjectPropertyRule();
    }
}
