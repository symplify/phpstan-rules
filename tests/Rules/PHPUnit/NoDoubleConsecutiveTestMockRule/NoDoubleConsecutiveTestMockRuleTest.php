<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoDoubleConsecutiveTestMockRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PHPUnit\NoDoubleConsecutiveTestMockRule;

final class NoDoubleConsecutiveTestMockRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/WillReturnAndWithConsecutive.php', [[NoDoubleConsecutiveTestMockRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipWillReturnCallback.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoDoubleConsecutiveTestMockRule();
    }
}
