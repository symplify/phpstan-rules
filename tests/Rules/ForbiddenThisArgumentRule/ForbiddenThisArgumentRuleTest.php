<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\ForbiddenThisArgumentRule;

final class ForbiddenThisArgumentRuleTest extends RuleTestCase
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
        yield 'SkipNativeFunctionCalls' => [__DIR__ . '/Fixture/SkipNativeFunctionCalls.php', []];
        yield 'SkipCustomFunctionCalls' => [__DIR__ . '/Fixture/SkipCustomFunctionCalls.php', []];
        yield 'SkipReflectionCalling' => [__DIR__ . '/Fixture/SkipReflectionCalling.php', []];
        yield 'SkipNotVariable' => [__DIR__ . '/Fixture/SkipNotVariable.php', []];
        yield 'SkipNotThis' => [__DIR__ . '/Fixture/SkipNotThis.php', []];
        yield 'SkipExtendsKernel' => [__DIR__ . '/Fixture/SkipExtendsKernel.php', []];

        yield 'StaticCall' => [__DIR__ . '/Fixture/StaticCall.php', [[ForbiddenThisArgumentRule::ERROR_MESSAGE, 13]]];
        yield 'ThisArgument' => [__DIR__ . '/Fixture/ThisArgument.php', [[ForbiddenThisArgumentRule::ERROR_MESSAGE, 11]]];
        yield 'ThisArgumentCopy' => [__DIR__ . '/Fixture/ThisArgumentCopy.php', [[ForbiddenThisArgumentRule::ERROR_MESSAGE, 12]]];
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
        return self::getContainer()->getByType(ForbiddenThisArgumentRule::class);
    }
}
