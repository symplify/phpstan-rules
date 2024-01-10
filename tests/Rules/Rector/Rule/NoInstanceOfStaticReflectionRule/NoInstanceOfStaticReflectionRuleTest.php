<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoInstanceOfStaticReflectionRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\PHPStanRules\Rule\NoInstanceOfStaticReflectionRule;

final class NoInstanceOfStaticReflectionRuleTest extends RuleTestCase
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
        $errorMessage = NoInstanceOfStaticReflectionRule::ERROR_MESSAGE;
        yield [__DIR__ . '/Fixture/InstanceofWithType.php', [[$errorMessage, 13]]];

        $errorMessage = NoInstanceOfStaticReflectionRule::ERROR_MESSAGE;
        yield [__DIR__ . '/Fixture/IsAWithType.php', [[$errorMessage, 13]]];

        yield [__DIR__ . '/Fixture/SkipAssert.php', []];
        yield [__DIR__ . '/Fixture/SkipAllowedType.php', []];
        yield [__DIR__ . '/Fixture/SkipGenericNodeType.php', []];
        yield [__DIR__ . '/Fixture/SkipIsAGenericClassString.php', []];
        yield [__DIR__ . '/Fixture/SkipIsAsClassString.php', []];
        yield [__DIR__ . '/Fixture/SkipFileInfo.php', []];
        yield [__DIR__ . '/Fixture/SkipArrayClassString.php', []];
        yield [__DIR__ . '/Fixture/SkipReflection.php', []];
        yield [__DIR__ . '/Fixture/SkipDateTime.php', []];
        yield [__DIR__ . '/Fixture/SkipTypesArray.php', []];
        yield [__DIR__ . '/Fixture/SkipSymfony.php', []];
        yield [__DIR__ . '/Fixture/SkipPhpDocNode.php', []];
        yield [__DIR__ . '/Fixture/SkipPHPStanType.php', []];
        yield [__DIR__ . '/Fixture/SkipSelfType.php', []];
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoInstanceOfStaticReflectionRule::class);
    }
}
