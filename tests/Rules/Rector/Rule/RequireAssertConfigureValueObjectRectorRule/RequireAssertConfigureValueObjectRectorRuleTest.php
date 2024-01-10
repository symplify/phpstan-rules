<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\RequireAssertConfigureValueObjectRectorRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\PHPStanRules\Rule\RequireAssertConfigureValueObjectRectorRule;

final class RequireAssertConfigureValueObjectRectorRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/MissingConfigureWithAssert.php', [[RequireAssertConfigureValueObjectRectorRule::ERROR_MESSAGE, 20]]];

        yield [__DIR__ . '/Fixture/SkipConfigureWithAssert.php', []];
        yield [__DIR__ . '/Fixture/SkipConfigureWithAssertInstanceof.php', []];
        yield [__DIR__ . '/Fixture/SkipNoArray.php', []];
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(RequireAssertConfigureValueObjectRectorRule::class);
    }
}
