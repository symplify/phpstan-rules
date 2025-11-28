<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDoctrineListenerWithoutContractRule;

use Iterator;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\NoDoctrineListenerWithoutContractRule;

final class NoDoctrineListenerWithoutContractRuleTest extends RuleTestCase
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
        yield [[__DIR__ . '/Fixture/SkipContractAwareListener.php'], []];
        yield [[__DIR__ . '/Fixture/SkipNonDoctrineEvent.php'], []];

        yield [[__DIR__ . '/Fixture/SimpleDoctrineListener.php'], [[
            NoDoctrineListenerWithoutContractRule::ERROR_MESSAGE,
            5,
        ]]];
    }

    protected function getRule(): NoDoctrineListenerWithoutContractRule
    {
        return new NoDoctrineListenerWithoutContractRule();
    }
}
