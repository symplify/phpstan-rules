<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoServiceJugglingRule;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Source\SomeUserHelper;

/**
 * @extends RuleTestCase<NoServiceJugglingRule>
 */
final class NoServiceJugglingRuleTest extends RuleTestCase
{
    /**
     * @param list<array{0: string, 1: int}> $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        $errorMessage = sprintf(
            NoServiceJugglingRule::ERROR_MESSAGE,
            'userHelper',
            'handle',
            SomeUserHelper::class
        );

        yield [__DIR__ . '/Fixture/PromotedJugglingService.php', [[$errorMessage, 18]]];
        yield [__DIR__ . '/Fixture/AutowiredJugglingService.php', [[$errorMessage, 21]]];

        yield [__DIR__ . '/Fixture/SkippedJugglingService.php', []];
        yield [__DIR__ . '/Fixture/TraitJugglingService.php', []];
        yield [__DIR__ . '/Fixture/VaryingArgumentJugglingService.php', []];
        yield [__DIR__ . '/Fixture/InheritedMethodJugglingService.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoServiceJugglingRule();
    }
}
