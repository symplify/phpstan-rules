<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoParentRepositoryRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\Handyman\PHPStan\Rule\NoParentRepositoryRule;

final class NoParentRepositoryRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeRepository.php', [[NoParentRepositoryRule::ERROR_MESSAGE, 9]]];
    }

    protected function getRule(): Rule
    {
        return new NoParentRepositoryRule();
    }
}
