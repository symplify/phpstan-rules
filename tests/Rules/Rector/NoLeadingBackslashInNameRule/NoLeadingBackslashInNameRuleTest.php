<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoLeadingBackslashInNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Rector\NoLeadingBackslashInNameRule;

final class NoLeadingBackslashInNameRuleTest extends RuleTestCase
{
    /**
     * @param array<int, array<string|int>> $expectedErrorsWithLines
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
        $errorMessage = NoLeadingBackslashInNameRule::ERROR_MESSAGE;
        yield [__DIR__ . '/Fixture/LeadingBackslashInName.php', [[$errorMessage, 13]]];

        yield [__DIR__ . '/Fixture/SkipNoBackslash.php', []];
        yield [__DIR__ . '/Fixture/SkipUseFullyQualified.php', []];
    }

    /**
     * @return array<int, string>
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoLeadingBackslashInNameRule::class);
    }
}
