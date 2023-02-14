<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNullableArrayPropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoNullableArrayPropertyRule;

/**
 * @extends RuleTestCase<NoNullableArrayPropertyRule>
 */
final class NoNullableArrayPropertyRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNoType.php', []];
        yield [__DIR__ . '/Fixture/SkipNotNullable.php', []];
        yield [__DIR__ . '/Fixture/SkipNotArray.php', []];
        yield [__DIR__ . '/Fixture/SkipClassNameProperty.php', []];
        yield [__DIR__ . '/Fixture/NullableArrayProperty.php', [[NoNullableArrayPropertyRule::ERROR_MESSAGE, 9]]];
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
        return self::getContainer()->getByType(NoNullableArrayPropertyRule::class);
    }
}
