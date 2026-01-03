<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoConstructorAndRequiredTogetherRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoConstructorAndRequiredTogetherRule;

final class NoConstructorAndRequiredTogetherRuleTest extends RuleTestCase
{
    /**
     * @param array<int, array<string|int>> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/ConstructorAndRequiredInSingleClass.php', [[
            NoConstructorAndRequiredTogetherRule::ERROR_MESSAGE,
            7,
        ]]];

        yield [__DIR__ . '/Fixture/SkipSoleMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipOtherSoleMethod.php', []];

        yield [__DIR__ . '/Fixture/SkipCircularDependencyPrevention.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoConstructorAndRequiredTogetherRule();
    }
}
