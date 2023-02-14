<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoNetteInjectAndConstructorRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Nette\Rules\NoNetteInjectAndConstructorRule;

/**
 * @extends RuleTestCase<NoNetteInjectAndConstructorRule>
 */
final class NoNetteInjectAndConstructorRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipOnlyConstructor.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstract.php', []];

        yield [__DIR__ . '/Fixture/InjectMethodAndConstructor.php', [
            [NoNetteInjectAndConstructorRule::ERROR_MESSAGE, 7],
        ]];

        yield [__DIR__ . '/Fixture/InjectAttributePropertyAndConstructor.php', [
            [NoNetteInjectAndConstructorRule::ERROR_MESSAGE, 9],
        ]];

        yield [__DIR__ . '/Fixture/InjectPropertyAndConstructor.php', [
            [NoNetteInjectAndConstructorRule::ERROR_MESSAGE, 7],
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
        return self::getContainer()->getByType(NoNetteInjectAndConstructorRule::class);
    }
}
