<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoNetteArrayAccessInControlRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Nette\Rules\NoNetteArrayAccessInControlRule;

final class NoNetteArrayAccessInControlRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNoArrayDimFetch.php', []];
        yield [__DIR__ . '/Fixture/SkipDimFetchOutsideNette.php', []];

        yield [__DIR__ . '/Fixture/ArrayDimFetchInForm.php', [
            [NoNetteArrayAccessInControlRule::ERROR_MESSAGE, 13],
        ]];

        yield [__DIR__ . '/Fixture/ArrayDimFetchInPresenter.php', [
            [NoNetteArrayAccessInControlRule::ERROR_MESSAGE, 13],
        ]];

        yield [__DIR__ . '/Fixture/ArrayDimFetchInControl.php', [
            [NoNetteArrayAccessInControlRule::ERROR_MESSAGE, 13],
        ]];
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
        return self::getContainer()->getByType(NoNetteArrayAccessInControlRule::class);
    }
}
