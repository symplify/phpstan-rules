<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoPropertyNodeAssignRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Rector\NoPropertyNodeAssignRule;

final class NoPropertyNodeAssignRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeNodePropertyAssign.php', [
            [NoPropertyNodeAssignRule::ERROR_MESSAGE, 20],
        ]];

        yield [__DIR__ . '/Fixture/SkipNoRectorAssign.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoPropertyNodeAssignRule();
    }
}
