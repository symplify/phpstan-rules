<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckAttributteArgumentClassExistsRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\CheckAttributteArgumentClassExistsRule;

final class CheckAttributteArgumentClassExistsRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipExistingClassAttributeArgument.php', []];

        yield [__DIR__ . '/Fixture/SomeClassWithAttributeArgumentMissingClass.php', [
            [CheckAttributteArgumentClassExistsRule::ERROR_MESSAGE, 9],
        ]];
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
        return self::getContainer()->getByType(CheckAttributteArgumentClassExistsRule::class);
    }
}
