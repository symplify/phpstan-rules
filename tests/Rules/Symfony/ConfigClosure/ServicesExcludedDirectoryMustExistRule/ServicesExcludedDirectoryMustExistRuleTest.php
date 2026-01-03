<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\ServicesExcludedDirectoryMustExistRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\ServicesExcludedDirectoryMustExistRule;

final class ServicesExcludedDirectoryMustExistRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipInvalidPath.php', []];
        yield [__DIR__ . '/Fixture/SkipMaskPath.php', []];

        yield [__DIR__ . '/Fixture/SomeConfigWithInvalidExclude.php', [
            [
                sprintf(ServicesExcludedDirectoryMustExistRule::ERROR_MESSAGE, '/../missing'),
                11,
            ],
        ]];
    }

    protected function getRule(): Rule
    {
        return new ServicesExcludedDirectoryMustExistRule();
    }
}
