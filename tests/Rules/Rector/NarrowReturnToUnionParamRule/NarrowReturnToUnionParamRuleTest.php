<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NarrowReturnToUnionParamRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Rector\NarrowReturnToUnionParamRule;

final class NarrowReturnToUnionParamRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipBroadReturn.php', []];

        yield [__DIR__ . '/Fixture/BroadReturnOfUnionParam.php', [
            [
                sprintf(NarrowReturnToUnionParamRule::ERROR_MESSAGE, 'PhpParser\Node', 'MethodCall|New_|null'),
                13,
            ],
        ]];
    }

    protected function getRule(): Rule
    {
        return new NarrowReturnToUnionParamRule();
    }
}
