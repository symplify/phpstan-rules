<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\ValidNetteInjectRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Nette\Rules\ValidNetteInjectRule;

final class ValidNetteInjectRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCorrectInject.php', []];
        yield [__DIR__ . '/Fixture/SkipCorrectInjectAttribute.php', []];

        yield [__DIR__ . '/Fixture/PrivateInjectMethod.php', [[ValidNetteInjectRule::ERROR_MESSAGE, 12]]];
        yield [__DIR__ . '/Fixture/PrivateInjectAttribute.php', [[ValidNetteInjectRule::ERROR_MESSAGE, 14]]];
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
        return self::getContainer()->getByType(ValidNetteInjectRule::class);
    }
}
