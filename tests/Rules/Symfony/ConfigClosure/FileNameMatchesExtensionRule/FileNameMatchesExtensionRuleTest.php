<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\FileNameMatchesExtensionRule;

use Iterator;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\FileNameMatchesExtensionRule;

final class FileNameMatchesExtensionRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/framework.php', []];
        yield [__DIR__ . '/Fixture/skip_no_extension.php', []];

        yield [__DIR__ . '/Fixture/wrong_name.php', [[
            sprintf(FileNameMatchesExtensionRule::ERROR_MESSAGE, 'framework', 'wrong_name'),
            10,
        ]]];
    }

    protected function getRule(): FileNameMatchesExtensionRule
    {
        return new FileNameMatchesExtensionRule();
    }
}
