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
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        $errorMessage = sprintf(StringFileAbsolutePathExistsRule::ERROR_MESSAGE, __DIR__ . '/Fixture/some_file.yml');
        yield [__DIR__ . '/Fixture/NonExistingFile.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/SkipReferenceToExistingFile.php', []];
        yield [__DIR__ . '/Fixture/SkipNestedConcats.php', []];
    }

    protected function getRule(): Rule
    {
        return new StringFileAbsolutePathExistsRule();
    }
}
