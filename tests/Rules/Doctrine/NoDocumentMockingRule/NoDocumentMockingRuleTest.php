<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDocumentMockingRule;

use Iterator;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\NoDocumentMockingRule;
use Symplify\PHPStanRules\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Source\Repository\SomeServiceRepository;

final class NoDocumentMockingRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(NoGetRepositoryOnServiceRepositoryEntityRule::ERROR_MESSAGE, 'SomeEntity', SomeServiceRepository::class);
        yield [__DIR__ . '/Fixture/SomeEntityMocking.php', [[
            $errorMessage,
            14,
        ]]];

        yield [__DIR__ . '/Fixture/SomeAbstractEntityMocking.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoDocumentMockingRule();
    }
}
