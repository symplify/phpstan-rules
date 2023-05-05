<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoFuncCallInMethodCallRule;

final class NoFuncCallInMethodCallRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(NoFuncCallInMethodCallRule::ERROR_MESSAGE, 'strlen');
        yield [__DIR__ . '/Fixture/FunctionCallNestedToMethodCall.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipGetCwd.php', []];
        yield [__DIR__ . '/Fixture/SkipNamespacedFunction.php', []];
        yield [__DIR__ . '/Fixture/SkipSprintfInCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipSymfonyStyleCommand.php', []];
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
        return self::getContainer()->getByType(NoFuncCallInMethodCallRule::class);
    }
}
