<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoPropertyNodeAssignRule;

use Iterator;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Rector\NoPropertyNodeAssignRule;

final class NoPropertyNodeAssignRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeNodePropertyAssign.php', [
            [NoPropertyNodeAssignRule::ERROR_MESSAGE, 20],
        ]];

        yield [__DIR__ . '/Fixture/SkipNoRectorAssign.php', []];
    }

    protected function getRule(): NoPropertyNodeAssignRule
    {
        return new NoPropertyNodeAssignRule();
    }
}
