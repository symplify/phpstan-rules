<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoReadonlyEntityClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\NoReadonlyEntityClassRule;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoReadonlyEntityClassRule\Fixture\ReportReadonlyAttributeEntity;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoReadonlyEntityClassRule\Fixture\ReportReadonlyLoadMetadataEntity;

final class NoReadonlyEntityClassRuleTest extends RuleTestCase
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
        $loadMetadataError = sprintf(
            NoReadonlyEntityClassRule::ERROR_MESSAGE,
            ReportReadonlyLoadMetadataEntity::class
        );
        yield [__DIR__ . '/Fixture/ReportReadonlyLoadMetadataEntity.php', [[$loadMetadataError, 7]]];

        $attributeError = sprintf(
            NoReadonlyEntityClassRule::ERROR_MESSAGE,
            ReportReadonlyAttributeEntity::class
        );
        yield [__DIR__ . '/Fixture/ReportReadonlyAttributeEntity.php', [[$attributeError, 9]]];

        yield [__DIR__ . '/Fixture/SkipReadonlyPlainClass.php', []];
        yield [__DIR__ . '/Fixture/SkipNonReadonlyEntity.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoReadonlyEntityClassRule();
    }
}
