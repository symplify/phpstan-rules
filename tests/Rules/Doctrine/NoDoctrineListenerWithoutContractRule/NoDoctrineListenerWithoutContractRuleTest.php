<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDoctrineListenerWithoutContractRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\NoDoctrineListenerWithoutContractRule;

final class NoDoctrineListenerWithoutContractRuleTest extends RuleTestCase
{
    /**
     * @param string[] $filePaths
     * @param array<int, array<string|int>> $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(array $filePaths, array $expectedErrorsWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<array<int, array<int, mixed>>, mixed>>
     */
    public static function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SkipContractAwareListener.php'], []];
        yield [[__DIR__ . '/Fixture/SkipNonDoctrineEvent.php'], []];

        yield [[__DIR__ . '/Fixture/SimpleDoctrineListener.php'], [[
            NoDoctrineListenerWithoutContractRule::ERROR_MESSAGE,
            5,
        ]]];
    }

    protected function getRule(): Rule
    {
        return new NoDoctrineListenerWithoutContractRule();
    }
}
