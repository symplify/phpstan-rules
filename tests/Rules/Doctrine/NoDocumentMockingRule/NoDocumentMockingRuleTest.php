<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDocumentMockingRule;

use Iterator;
<<<<<<< HEAD
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\NoDocumentMockingRule;
=======
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\NoDocumentMockingRule;
use Symplify\PHPStanRules\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Source\Repository\SomeServiceRepository;
>>>>>>> 0ffdc220 (add test)

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
<<<<<<< HEAD
        yield [__DIR__ . '/Fixture/SomeEntityMocking.php', [[
            NoDocumentMockingRule::ERROR_MESSAGE,
=======
        $errorMessage = sprintf(NoGetRepositoryOnServiceRepositoryEntityRule::ERROR_MESSAGE, 'SomeEntity', SomeServiceRepository::class);
        yield [__DIR__ . '/Fixture/SomeEntityMocking.php', [[
            $errorMessage,
>>>>>>> 0ffdc220 (add test)
            14,
        ]]];

        yield [__DIR__ . '/Fixture/SomeAbstractEntityMocking.php', []];
    }

<<<<<<< HEAD
    /**
     * @return string[]
     */
    #[Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoDocumentMockingRule::class);
=======
    protected function getRule(): Rule
    {
<<<<<<< HEAD
        $reflectionProvider = self::getContainer()->getByType(ReflectionProvider::class);

        return new NoGetRepositoryOnServiceRepositoryEntityRule($reflectionProvider);
>>>>>>> 0ffdc220 (add test)
=======
        return new NoDocumentMockingRule();
>>>>>>> d892b6fc (add test)
    }
}
