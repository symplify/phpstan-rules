<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoParentRepositoryRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\NoParentRepositoryRule;

final class NoParentRepositoryRuleTest extends RuleTestCase
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
     * @return Iterator<(array<int, array<int, array<int, int>>>|array<int, array<int, array<int, string>>>|array<int, string>)>
     */
    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeRepository.php', [[NoParentRepositoryRule::ERROR_MESSAGE, 9]]];
    }

    protected function getRule(): Rule
    {
        return new NoParentRepositoryRule();
    }
}
