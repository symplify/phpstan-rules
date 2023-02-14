<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Nette\Rules\ForbiddenNetteInjectOverrideRule;

final class ForbiddenNetteInjectOverrideRuleTest extends RuleTestCase
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
        yield [
            __DIR__ . '/Fixture/OverrideParentInject.php',
            [[ForbiddenNetteInjectOverrideRule::ERROR_MESSAGE, 14]],
        ];

        yield [
            __DIR__ . '/Fixture/OverrideParentInjectAttribute.php',
            [[ForbiddenNetteInjectOverrideRule::ERROR_MESSAGE, 13]],
        ];

        yield [
            __DIR__ . '/Fixture/OverrideParentInjectClassMethodAttribute.php',
            [[ForbiddenNetteInjectOverrideRule::ERROR_MESSAGE, 14]],
        ];

        yield [__DIR__ . '/Fixture/SkipNonInjectAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipCurrentMethodInject.php', []];
        yield [__DIR__ . '/Fixture/SkipParentAnnotatedProperty.php', []];
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
        return self::getContainer()->getByType(ForbiddenNetteInjectOverrideRule::class);
    }
}
