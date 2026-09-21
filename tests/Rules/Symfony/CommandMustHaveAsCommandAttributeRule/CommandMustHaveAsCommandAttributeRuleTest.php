<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\CommandMustHaveAsCommandAttributeRule;

use Iterator;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\CommandMustHaveAsCommandAttributeRule;
use Symplify\PHPStanRules\Tests\Rules\Symfony\CommandMustHaveAsCommandAttributeRule\Fixture\ReportCommand;

/**
 * @extends RuleTestCase<CommandMustHaveAsCommandAttributeRule>
 */
final class CommandMustHaveAsCommandAttributeRuleTest extends RuleTestCase
{
    /**
     * @param list<array{0: string, 1: int}> $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        $errorMessage = sprintf(
            CommandMustHaveAsCommandAttributeRule::ERROR_MESSAGE,
            ReportCommand::class
        );
        yield [__DIR__ . '/Fixture/ReportCommand.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/SkipCommandWithAttribute.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipNonCommand.php', []];
    }

    /**
     * @return string[]
     */
    #[Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(CommandMustHaveAsCommandAttributeRule::class);
    }
}
