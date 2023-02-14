<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoNetteDoubleTemplateAssignRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Nette\Rules\NoNetteDoubleTemplateAssignRule;

final class NoNetteDoubleTemplateAssignRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipIfElseAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipUniqueAssignPresenter.php', []];
        yield [__DIR__ . '/Fixture/SkipNoPresenter.php', []];

        $errorMessage = sprintf(NoNetteDoubleTemplateAssignRule::ERROR_MESSAGE, 'key');
        yield [__DIR__ . '/Fixture/DoubleAssignPresenter.php', [[$errorMessage, 11]]];
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
        return self::getContainer()->getByType(NoNetteDoubleTemplateAssignRule::class);
    }
}
