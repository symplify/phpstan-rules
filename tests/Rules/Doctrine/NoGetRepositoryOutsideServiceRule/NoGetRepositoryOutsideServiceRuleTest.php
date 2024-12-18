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
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/NonRepositoryUsingEntityManager.php', [[
            NoGetRepositoryOutsideServiceRule::ERROR_MESSAGE,
            18,
        ]]];

        yield [__DIR__ . '/Fixture/SkipInRepository.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoGetRepositoryOutsideServiceRule();
    }
}
