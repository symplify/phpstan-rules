<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOutsideServiceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\NoGetRepositoryOutsideServiceRule;

final class NoGetRepositoryOutsideServiceRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/NonRepositoryUsingEntityManager.php', [[
            NoGetRepositoryOutsideServiceRule::ERROR_MESSAGE,
            18,
        ]]];

        yield [__DIR__ . '/Fixture/GetRepositoryRandomEntity.php', [[
            NoGetRepositoryOutsideServiceRule::ERROR_MESSAGE,
            19,
        ]]];

        yield [__DIR__ . '/Fixture/SkipInRepository.php', []];
        yield [__DIR__ . '/Fixture/SkipDynamicFetch.php', []];
        yield [__DIR__ . '/Fixture/SkipDynamicClassConstFetch.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoGetRepositoryOutsideServiceRule();
    }
}
