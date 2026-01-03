<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSameNameSetClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\NoServiceSameNameSetClassRule;

final class NoServiceSameNameSetClassRuleTest extends RuleTestCase
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
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeConfigWithInvalidSet.php', [
            [
                sprintf(NoServiceSameNameSetClassRule::ERROR_MESSAGE, 'SomeSetService'),
                11,
            ],
        ]];

        yield [__DIR__ . '/Fixture/SkipNonClosureConstantSet.php', []];
        yield [__DIR__ . '/Fixture/SkipParametersSetConstant.php', []];
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
        return self::getContainer()->getByType(NoServiceSameNameSetClassRule::class);
    }
}
