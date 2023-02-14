<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\RequireNamedCommandRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Symfony\Rules\RequireNamedCommandRule;

final class RequireNamedCommandRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNamedCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractMissingNameCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipAttributeNamedCommand.php', []];

        yield [__DIR__ . '/Fixture/MissingNameCommand.php', [[RequireNamedCommandRule::ERROR_MESSAGE, 9]]];
        yield [
            __DIR__ . '/Fixture/MissingNameCommandWithoutConfigureMethod.php',
            [[RequireNamedCommandRule::ERROR_MESSAGE, 9]],
        ];
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
        return self::getContainer()->getByType(RequireNamedCommandRule::class);
    }
}
