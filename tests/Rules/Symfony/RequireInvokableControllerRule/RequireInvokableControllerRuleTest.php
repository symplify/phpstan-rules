<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireInvokableControllerRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\RequireInvokableControllerRule;

final class RequireInvokableControllerRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipInvokableController.php', []];
        yield [__DIR__ . '/Fixture/SkipRandomPublicMethodController.php', []];

        yield [__DIR__ . '/Fixture/MissnamedController.php', [[RequireInvokableControllerRule::ERROR_MESSAGE, 14]]];
        yield [__DIR__ . '/Fixture/MultipleMethodsController.php', [
            [RequireInvokableControllerRule::ERROR_MESSAGE, 14],
            [RequireInvokableControllerRule::ERROR_MESSAGE, 21],
        ]];
    }

    protected function getRule(): Rule
    {
        return new RequireInvokableControllerRule();
    }
}
