<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayAccessOnObjectRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoArrayAccessOnObjectRule;

final class NoArrayAccessOnObjectRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ArrayAccessOnObject.php', [[NoArrayAccessOnObjectRule::ERROR_MESSAGE, 14]]];
        yield [__DIR__ . '/Fixture/ArrayAccessOnNestedObject.php', [[NoArrayAccessOnObjectRule::ERROR_MESSAGE, 14]]];

        yield [__DIR__ . '/Fixture/SkipOnArray.php', []];
        yield [__DIR__ . '/Fixture/SkipSplFixedArray.php', []];
        yield [__DIR__ . '/Fixture/SkipXml.php', []];
        yield [__DIR__ . '/Fixture/SkipXmlElementForeach.php', []];
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
        return self::getContainer()->getByType(NoArrayAccessOnObjectRule::class);
    }
}
