<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\SingleArgEventDispatchRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\Handyman\PHPStan\Rule\SingleArgEventDispatchRule;

final class SingleArgEventDispatchRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/ReportEventDispatcher.php', [[SingleArgEventDispatchRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipSingleDispatch.php', []];
        yield [__DIR__ . '/Fixture/SkipUnrelatedDispatch.php', []];
    }

    protected function getRule(): Rule
    {
        return new SingleArgEventDispatchRule();
    }
}
