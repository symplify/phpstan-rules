<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoListenerWithoutContractRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoListenerWithoutContractRule;

final class NoListenerWithoutContractRuleTest extends RuleTestCase
{
    /**
     * @param string[] $filePaths
     */
    #[DataProvider('provideData')]
    public function testRule(array $filePaths, array $expectedErrorsWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SkipFormListener.php'], []];

        yield [[__DIR__ . '/Fixture/SomeContractedListener.php'], []];
        yield [[__DIR__ . '/Fixture/SomeContractedWithAttributeListener.php'], []];
        yield [[__DIR__ . '/Fixture/SkipDoctrineListener.php'], []];

        yield [[__DIR__ . '/Fixture/SomeBareListener.php'], [[
            NoListenerWithoutContractRule::ERROR_MESSAGE,
            5,
        ]]];
    }

    protected function getRule(): Rule
    {
        return new NoListenerWithoutContractRule();
    }
}
