<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule;

use Iterator;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Source\Repository\SomeServiceRepository;

final class NoGetRepositoryOnServiceRepositoryEntityRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(NoGetRepositoryOnServiceRepositoryEntityRule::ERROR_MESSAGE, 'SomeEntity', SomeServiceRepository::class);
        yield [__DIR__ . '/Fixture/GetRepositoryOnServiceAwareEntity.php', [[
            $errorMessage,
            14,
        ]]];

        $errorMessage = sprintf(NoGetRepositoryOnServiceRepositoryEntityRule::ERROR_MESSAGE, 'SomeEntity', SomeServiceRepository::class);
        yield [__DIR__ . '/Fixture/GetServiceInterfaceRepository.php', [[
            $errorMessage,
            14,
        ]]];

        yield [__DIR__ . '/Fixture/SkipGetRepositoryOnNormalRepository.php', []];
    }

    protected function getRule(): NoGetRepositoryOnServiceRepositoryEntityRule
    {
        $reflectionProvider = self::getContainer()->getByType(ReflectionProvider::class);

        return new NoGetRepositoryOnServiceRepositoryEntityRule($reflectionProvider);
    }
}
