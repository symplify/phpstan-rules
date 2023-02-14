<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoRightPHPUnitAssertScalarRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PHPUnit\NoRightPHPUnitAssertScalarRule;

final class NoRightPHPUnitAssertScalarRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipCorrectAssert.php', []];

        yield [__DIR__ . '/Fixture/SomeFlippedAssert.php', [[NoRightPHPUnitAssertScalarRule::ERROR_MESSAGE, 14]]];
        yield [
            __DIR__ . '/Fixture/FlippedAssertWithConstFetch.php',
            [[NoRightPHPUnitAssertScalarRule::ERROR_MESSAGE, 17]], ];
        yield [__DIR__ . '/Fixture/SomeBoolAssert.php', [[NoRightPHPUnitAssertScalarRule::ERROR_MESSAGE, 14]]];
    }

    protected function getRule(): Rule
    {
        return new NoRightPHPUnitAssertScalarRule();
    }
}
