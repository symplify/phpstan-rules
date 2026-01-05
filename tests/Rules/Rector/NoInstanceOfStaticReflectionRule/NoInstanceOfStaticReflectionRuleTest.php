<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoInstanceOfStaticReflectionRule;

use Iterator;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Rector\NoInstanceOfStaticReflectionRule;

final class NoInstanceOfStaticReflectionRuleTest extends RuleTestCase
{
    /**
     * @param array<int, array<string|int>> $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        $errorMessage = NoInstanceOfStaticReflectionRule::ERROR_MESSAGE;
        yield [__DIR__ . '/Fixture/InstanceofWithType.php', [[$errorMessage, 13]]];

        $errorMessage = NoInstanceOfStaticReflectionRule::ERROR_MESSAGE;
        yield [__DIR__ . '/Fixture/IsAWithType.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipAllowedType.php', []];
        yield [__DIR__ . '/Fixture/SkipGenericNodeType.php', []];
        yield [__DIR__ . '/Fixture/SkipIsAGenericClassString.php', []];
        yield [__DIR__ . '/Fixture/SkipIsAsClassString.php', []];
        yield [__DIR__ . '/Fixture/SkipArrayClassString.php', []];
        yield [__DIR__ . '/Fixture/SkipReflection.php', []];
        yield [__DIR__ . '/Fixture/SkipTypesArray.php', []];
        yield [__DIR__ . '/Fixture/SkipSymfony.php', []];
        yield [__DIR__ . '/Fixture/SkipPhpDocNode.php', []];
        yield [__DIR__ . '/Fixture/SkipPHPStanType.php', []];
        yield [__DIR__ . '/Fixture/SkipSelfType.php', []];
    }

    /**
     * @return array<int, string>
     */
    #[Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoInstanceOfStaticReflectionRule::class);
    }
}
