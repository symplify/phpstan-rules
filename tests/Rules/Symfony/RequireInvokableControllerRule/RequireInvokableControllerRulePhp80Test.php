<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireInvokableControllerRule;

use Iterator;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\RequireInvokableControllerRule;

final class RequireInvokableControllerRulePhp80Test extends RuleTestCase
{
    /**
     * @param array<int, array<string|int>> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<(array<int, array<int, array<int, int>>>|array<int, array<int, array<int, string>>>|array<int, string>)>
     */
    public static function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/MissnamedRouteAttributeController.php',
            [[RequireInvokableControllerRule::ERROR_MESSAGE, 12]],
        ];
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
        return self::getContainer()->getByType(RequireInvokableControllerRule::class);
    }
}
