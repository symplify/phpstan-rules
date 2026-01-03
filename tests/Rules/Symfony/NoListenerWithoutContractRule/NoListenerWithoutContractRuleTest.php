<?php

declare(strict_types=1);

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
        // @see https://symfony.com/blog/new-in-symfony-4-1-invokable-event-listeners
        yield [[__DIR__ . '/Fixture/SkipInvokableListener.php'], []];

        yield [[__DIR__ . '/Fixture/SkipSecurityListener.php'], []];
        yield [[__DIR__ . '/Fixture/SkipAnotherSecurityListener.php'], []];
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
