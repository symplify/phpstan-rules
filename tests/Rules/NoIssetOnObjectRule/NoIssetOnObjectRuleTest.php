<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoIssetOnObjectRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoIssetOnObjectRule;

final class NoIssetOnObjectRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/IssetOnObject.php', [[NoIssetOnObjectRule::ERROR_MESSAGE, 17]]];

        yield [__DIR__ . '/Fixture/SkipIssetOnArray.php', []];
        yield [__DIR__ . '/Fixture/SkipIssetOnArrayNestedOnObject.php', []];
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
        return self::getContainer()->getByType(NoIssetOnObjectRule::class);
    }
}
