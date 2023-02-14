<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMissingDirPathRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoMissingDirPathRule;

/**
 * @extends RuleTestCase<NoMissingDirPathRule>
 */
final class NoMissingDirPathRuleTest extends RuleTestCase
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
        $message = sprintf(NoMissingDirPathRule::ERROR_MESSAGE, '/not_here.php');
        yield [__DIR__ . '/Fixture/FileMissing.php', [[$message, 11]]];

        yield [__DIR__ . '/Fixture/SkipBracketPathFromSymfonyConfigImport.php', []];
        yield [__DIR__ . '/Fixture/SkipConcat.php', []];
        yield [__DIR__ . '/Fixture/SkipVendorAutoload.php', []];
        yield [__DIR__ . '/Fixture/SkipVendorAutoload.php', []];
        yield [__DIR__ . '/Fixture/SkipAssertMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipFnMatch.php', []];
        yield [__DIR__ . '/Fixture/SkipFileExistsFuncCall.php', []];
        yield [__DIR__ . '/Fixture/SkipFileExistsFuncCallOneLayerAbove.php', []];
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
        return self::getContainer()->getByType(NoMissingDirPathRule::class);
    }
}
