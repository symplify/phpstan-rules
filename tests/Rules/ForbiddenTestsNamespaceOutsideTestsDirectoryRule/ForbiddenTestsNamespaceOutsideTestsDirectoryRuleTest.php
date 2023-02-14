<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenTestsNamespaceOutsideTestsDirectoryRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\ForbiddenTestsNamespaceOutsideTestsDirectoryRule;

/**
 * @extends RuleTestCase<ForbiddenTestsNamespaceOutsideTestsDirectoryRule>
 */
final class ForbiddenTestsNamespaceOutsideTestsDirectoryRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<int, mixed[]|string>>
     */
    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/tests/SkipTestsNamespaceInsideTestsDirectoryClass.php', []];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(ForbiddenTestsNamespaceOutsideTestsDirectoryRule::class);
    }
}
