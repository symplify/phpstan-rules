<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoClassReflectionStaticReflectionRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\PHPStanRules\Rule\NoClassReflectionStaticReflectionRule;

final class NoClassReflectionStaticReflectionRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/NewOnExternal.php', [[NoClassReflectionStaticReflectionRule::ERROR_MESSAGE, 13]]];

        yield [__DIR__ . '/Fixture/SkipAllowedType.php', []];
        yield [__DIR__ . '/Fixture/SkipNonReflectionNew.php', []];
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoClassReflectionStaticReflectionRule::class);
    }
}
