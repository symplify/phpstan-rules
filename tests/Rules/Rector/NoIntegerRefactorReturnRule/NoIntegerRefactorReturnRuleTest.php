<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoIntegerRefactorReturnRule;

use Iterator;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Rector\NoIntegerRefactorReturnRule;

final class NoIntegerRefactorReturnRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        $errorMessage = NoIntegerRefactorReturnRule::ERROR_MESSAGE;
        yield [__DIR__ . '/Fixture/SimpleReturnInt.php', [[$errorMessage, 20]]];

        yield [__DIR__ . '/Fixture/SkipBareNodeReturn.php', []];
        yield [__DIR__ . '/Fixture/AllowRemoveNode.php', []];
        yield [__DIR__ . '/Fixture/AllowNestedClosure.php', []];
    }

    protected function getRule(): NoIntegerRefactorReturnRule
    {
        return new NoIntegerRefactorReturnRule();
    }
}
