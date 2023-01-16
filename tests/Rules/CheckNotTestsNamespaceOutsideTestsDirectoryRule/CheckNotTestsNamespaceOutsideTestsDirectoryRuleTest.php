<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckNotTestsNamespaceOutsideTestsDirectoryRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\CheckNotTestsNamespaceOutsideTestsDirectoryRule;

/**
 * @extends RuleTestCase<CheckNotTestsNamespaceOutsideTestsDirectoryRule>
 */
final class CheckNotTestsNamespaceOutsideTestsDirectoryRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<int, mixed[]|string>>
     */
    public function provideData(): Iterator
    {
        // expect error
        yield [
            __DIR__ . '/Fixture/Fail/NotInTestsDirectory/FailNotInTestsDirectoryTest.php',
            [[CheckNotTestsNamespaceOutsideTestsDirectoryRule::ERROR_MESSAGE, 5]]];

        // Skip
        yield [__DIR__ . '/Fixture/Tests/SkipTestsNamespaceInsideTestsDirectoryClass.php', []];

        // Good
        yield [__DIR__ . '/Fixture/Pass/EndWithTests/Tests/NameSpaceEndWithTestsFileTest.php', []];
        yield [__DIR__ . '/Fixture/Pass/Tests/ContainsTestsDirectory/NameSpaceContainsTestsFileTest.php', []];
        yield [__DIR__ . '/Fixture/Tests/InTestsDirectory/NameSpaceStarttWithTestsFileTest.php', []];
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
        return self::getContainer()->getByType(CheckNotTestsNamespaceOutsideTestsDirectoryRule::class);
    }
}
