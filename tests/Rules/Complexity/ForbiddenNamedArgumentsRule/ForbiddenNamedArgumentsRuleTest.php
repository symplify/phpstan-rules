<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenNamedArgumentsRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Complexity\ForbiddenNamedArgumentsRule;

final class ForbiddenNamedArgumentsRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAttributeNamedArguments.php', []];
        yield [__DIR__ . '/Fixture/SkipNormalArguments.php', []];

        yield [
            __DIR__ . '/Fixture/ClassWithNamedArguments.php',
            [[ForbiddenNamedArgumentsRule::ERROR_MESSAGE, 11]],
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
        return self::getContainer()->getByType(ForbiddenNamedArgumentsRule::class);
    }
}
