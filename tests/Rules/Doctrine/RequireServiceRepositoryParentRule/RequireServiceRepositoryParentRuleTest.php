<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireServiceRepositoryParentRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Enum\DoctrineClass;
use Symplify\PHPStanRules\Rules\Doctrine\RequireServiceRepositoryParentRule;

final class RequireServiceRepositoryParentRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        $errorMessage = sprintf(RequireServiceRepositoryParentRule::ERROR_MESSAGE, DoctrineClass::ODM_SERVICE_REPOSITORY, DoctrineClass::ORM_SERVICE_REPOSITORY, DoctrineClass::ODM_SERVICE_REPOSITORY_INTERFACE);

        yield [__DIR__ . '/Fixture/SomeRepository.php', [[$errorMessage, 7]]];

        yield [__DIR__ . '/Fixture/SkipServiceRepository.php', []];
        yield [__DIR__ . '/Fixture/SkipContractImplementingRepository.php', []];
    }

    protected function getRule(): Rule
    {
        return new RequireServiceRepositoryParentRule();
    }
}
