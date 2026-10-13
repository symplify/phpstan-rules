<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RepositoryMethodMustHaveReturnTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\RepositoryMethodMustHaveReturnTypeRule;

/**
 * @extends RuleTestCase<RepositoryMethodMustHaveReturnTypeRule>
 */
final class RepositoryMethodMustHaveReturnTypeRuleTest extends RuleTestCase
{
    /**
     * @param list<array{0: string, 1: int}> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        $errorMessage = sprintf(RepositoryMethodMustHaveReturnTypeRule::ERROR_MESSAGE, 'findActive');

        yield [__DIR__ . '/Fixture/SomeRepository.php', [[$errorMessage, 9]]];
        yield [__DIR__ . '/Fixture/SkipRepository.php', []];
        yield [__DIR__ . '/Fixture/SkipNonRepositoryClass.php', []];
    }

    protected function getRule(): Rule
    {
        return new RepositoryMethodMustHaveReturnTypeRule();
    }
}
