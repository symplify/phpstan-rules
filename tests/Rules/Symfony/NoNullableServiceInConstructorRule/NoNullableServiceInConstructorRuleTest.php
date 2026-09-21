<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoNullableServiceInConstructorRule;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\AnotherService;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\SomeService;

/**
 * @extends RuleTestCase<NoNullableServiceInConstructorRule>
 */
final class NoNullableServiceInConstructorRuleTest extends RuleTestCase
{
    /**
     * @param list<array{0: string, 1: int}> $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        $someServiceError = sprintf(
            NoNullableServiceInConstructorRule::ERROR_MESSAGE,
            '$someService',
            SomeService::class
        );
        $anotherServiceError = sprintf(
            NoNullableServiceInConstructorRule::ERROR_MESSAGE,
            '$anotherService',
            AnotherService::class
        );

        yield [__DIR__ . '/Fixture/ReportNullableService.php', [[$someServiceError, 13], [$anotherServiceError, 14]]];

        yield [__DIR__ . '/Fixture/SkipNullableScalar.php', []];
        yield [__DIR__ . '/Fixture/SkipNullableException.php', []];
        yield [__DIR__ . '/Fixture/SkipNullableDateTime.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractClass.php', []];
        yield [__DIR__ . '/Fixture/SkipDuplicateType.php', []];
        yield [__DIR__ . '/Fixture/SkipAnonymousClass.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoNullableServiceInConstructorRule($this->createReflectionProvider());
    }
}
