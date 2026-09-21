<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoStringTargetEntityRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\NoStringTargetEntityRule;

final class NoStringTargetEntityRuleTest extends RuleTestCase
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
            NoStringTargetEntityRule::ERROR_MESSAGE,
            'ManyToOne',
            'App\Entity\Category',
            'App\Entity\Category'
        );
        yield [__DIR__ . '/Fixture/ReportStringTargetEntity.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipClassConstTargetEntity.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoStringTargetEntityRule();
    }
}
