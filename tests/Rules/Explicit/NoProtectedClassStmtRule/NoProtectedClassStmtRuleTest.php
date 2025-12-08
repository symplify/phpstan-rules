<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoProtectedClassStmtRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Explicit\NoProtectedClassStmtRule;

final class NoProtectedClassStmtRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/ClassWithProtected.php', [
            [NoProtectedClassStmtRule::ERROR_MESSAGE, 7],
        ]];

        yield [__DIR__ . '/Fixture/SkipPHPUnitTetsCase.php', []];
        yield [__DIR__ . '/Fixture/SkipParentRequired.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractWithProtected.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoProtectedClassStmtRule();
    }
}
