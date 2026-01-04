<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMissnamedDocTagRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoMissnamedDocTagRule;

final class NoMissnamedDocTagRuleTest extends RuleTestCase
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
     * @return Iterator<mixed>
     */
    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/ClassMethodNonReturn.php', [
            [sprintf(NoMissnamedDocTagRule::METHOD_ERROR_MESSAGE, '@var'), 12],
        ]];

        yield [__DIR__ . '/Fixture/SomeClass.php', [
            [sprintf(NoMissnamedDocTagRule::PROPERTY_ERROR_MESSAGE, '@return'), 12],
        ]];

        yield [__DIR__ . '/Fixture/SomeConstant.php', [
            [sprintf(NoMissnamedDocTagRule::CONSTANT_ERROR_MESSAGE, '@return'), 12],
        ]];

        yield [__DIR__ . '/Fixture/SkipValidPropertyTag.php', []];
    }

    /**
     * @return array<int, string>
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoMissnamedDocTagRule::class);
    }
}
