<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\SingleRequiredMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\SingleRequiredMethodRule;

final class SingleRequiredMethodRuleTest extends RuleTestCase
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
        $expectedErrorMessage = sprintf(SingleRequiredMethodRule::ERROR_MESSAGE, 2);

        yield [__DIR__ . '/Fixture/MultipleRequiredMethods.php', [[$expectedErrorMessage, 5]]];
        yield [__DIR__ . '/Fixture/MultipleRequiredAttributeMethods.php', [[$expectedErrorMessage, 7]]];

        yield [__DIR__ . '/Fixture/SkipSingleRequiredMethod.php', []];
    }

    protected function getRule(): Rule
    {
        return new SingleRequiredMethodRule();
    }
}
