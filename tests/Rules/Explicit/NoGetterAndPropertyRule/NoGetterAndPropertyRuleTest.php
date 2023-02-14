<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoGetterAndPropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Explicit\NoGetterAndPropertyRule;

final class NoGetterAndPropertyRuleTest extends RuleTestCase
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
        $boolGetterErrorMessage = \sprintf(NoGetterAndPropertyRule::ERROR_MESSAGE, 'enabled');
        yield [__DIR__ . '/Fixture/PublicAndIsser.php', [[$boolGetterErrorMessage, 7]]];

        $boolGetterErrorMessage = \sprintf(NoGetterAndPropertyRule::ERROR_MESSAGE, 'name');
        yield [__DIR__ . '/Fixture/SomeClassWithPublicAndGetter.php', [[$boolGetterErrorMessage, 7]]];

        yield [__DIR__ . '/Fixture/SkipPrivateMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipProtectedProperty.php', []];
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
        return self::getContainer()->getByType(NoGetterAndPropertyRule::class);
    }
}
