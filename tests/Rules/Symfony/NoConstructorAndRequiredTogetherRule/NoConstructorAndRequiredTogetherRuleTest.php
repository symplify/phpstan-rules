<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoConstructorAndRequiredTogetherRule;

use Iterator;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoConstructorAndRequiredTogetherRule;

final class NoConstructorAndRequiredTogetherRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

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

    protected function getRule(): NoConstructorAndRequiredTogetherRule
    {
        return new NoConstructorAndRequiredTogetherRule();
    }
}
