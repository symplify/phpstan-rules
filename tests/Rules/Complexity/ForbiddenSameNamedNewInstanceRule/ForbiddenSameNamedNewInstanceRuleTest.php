<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Complexity\ForbiddenSameNamedNewInstanceRule;

final class ForbiddenSameNamedNewInstanceRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipDifferentNames.php', []];
        yield [__DIR__ . '/Fixture/SkipNullDefaultAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipNonObjectAssigns.php', []];
        yield [__DIR__ . '/Fixture/SkipForeachNewNesting.php', []];
        yield [__DIR__ . '/Fixture/SkipForeachVariableAssign.php', []];

        $errorMessage = sprintf(ForbiddenSameNamedNewInstanceRule::ERROR_MESSAGE, '$someProduct');
        yield [__DIR__ . '/Fixture/SameObjectAssigns.php', [[$errorMessage, 14]]];
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
        return self::getContainer()->getByType(ForbiddenSameNamedNewInstanceRule::class);
    }
}
