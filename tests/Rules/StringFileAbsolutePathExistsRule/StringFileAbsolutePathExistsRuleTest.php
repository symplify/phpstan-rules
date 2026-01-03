<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\StringFileAbsolutePathExistsRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\StringFileAbsolutePathExistsRule;

final class StringFileAbsolutePathExistsRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(StringFileAbsolutePathExistsRule::ERROR_MESSAGE, __DIR__ . '/Fixture/some_file.yml');
        yield [__DIR__ . '/Fixture/NonExistingFile.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/SkipReferenceToExistingFile.php', []];
        yield [__DIR__ . '/Fixture/SkipNestedConcats.php', []];
        yield [__DIR__ . '/Fixture/SkipMasks.php', []];
        yield [__DIR__ . '/Fixture/SkipClosure.php', []];
    }

    protected function getRule(): Rule
    {
        return new StringFileAbsolutePathExistsRule();
    }
}
