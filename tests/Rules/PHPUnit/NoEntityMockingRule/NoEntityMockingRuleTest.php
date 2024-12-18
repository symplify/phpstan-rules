<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoEntityMockingRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\Handyman\PHPStan\Rule\NoEntityMockingRule;

final class NoEntityMockingRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/MockingEntity.php', [[NoEntityMockingRule::ERROR_MESSAGE, 12]]];

        yield [__DIR__ . '/Fixture/MockingDocument.php', [[NoEntityMockingRule::ERROR_MESSAGE, 12]]];

        yield [__DIR__ . '/Fixture/SkipMockingOtherObject.php', []];
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
        $container = self::getContainer();
        return $container->getByType(NoEntityMockingRule::class);
    }
}
