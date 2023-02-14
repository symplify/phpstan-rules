<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireConstantInMethodCallPositionRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Enum\RequireConstantInMethodCallPositionRule;

final class RequireConstantInMethodCallPositionRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipWithConstantLocal.php', []];
        yield [__DIR__ . '/Fixture/SkipWithConstantExternal.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];

        $errorMessageLocal = sprintf(RequireConstantInMethodCallPositionRule::ERROR_MESSAGE, 0);
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstantLocal.php', [[$errorMessageLocal, 14]]];

        $errorMessageExternal = sprintf(RequireConstantInMethodCallPositionRule::ERROR_MESSAGE, 0);
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstantExternal.php', [[$errorMessageExternal, 14]]];

        yield [__DIR__ . '/Fixture/SkipWithConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];

        $errorMessage = sprintf(RequireConstantInMethodCallPositionRule::ERROR_MESSAGE, 0);
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstant.php', [[$errorMessage, 14]]];
        yield [__DIR__ . '/Fixture/SymfonyPHPConfigParameterSetter.php', [[$errorMessage, 14]]];

        yield [__DIR__ . '/Fixture/NestedNode.php', [[$errorMessage, 14], [$errorMessage, 19]]];
        yield [__DIR__ . '/Fixture/IntersectionNode.php', [[$errorMessage, 17]]];
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
        return self::getContainer()->getByType(RequireConstantInMethodCallPositionRule::class);
    }
}
