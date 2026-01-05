<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReferenceRule;

use Iterator;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoReferenceRule;

final class NoReferenceRuleTest extends RuleTestCase
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
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/MethodWithReference.php', [[NoReferenceRule::ERROR_MESSAGE, 9]]];
        yield [__DIR__ . '/Fixture/FunctionWithReference.php', [[NoReferenceRule::ERROR_MESSAGE, 7]]];
        yield [__DIR__ . '/Fixture/VariableReference.php', [[NoReferenceRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/ReferenceArgument.php', [[NoReferenceRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/AssignReference.php', [[NoReferenceRule::ERROR_MESSAGE, 14]]];

        yield [__DIR__ . '/Fixture/SkipUseInReference.php', []];
        yield [__DIR__ . '/Fixture/SkipParentMethodWithReference.php', []];
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
        return self::getContainer()->getByType(NoReferenceRule::class);
    }
}
