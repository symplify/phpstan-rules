<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConstraintMustHaveAttributeRule;

use Iterator;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\ConstraintMustHaveAttributeRule;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConstraintMustHaveAttributeRule\Fixture\ReportConstraint;

/**
 * @extends RuleTestCase<ConstraintMustHaveAttributeRule>
 */
final class ConstraintMustHaveAttributeRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(
            ConstraintMustHaveAttributeRule::ERROR_MESSAGE,
            ReportConstraint::class
        );
        yield [__DIR__ . '/Fixture/ReportConstraint.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/SkipConstraintWithAttribute.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractConstraint.php', []];
        yield [__DIR__ . '/Fixture/SkipNonConstraint.php', []];
    }

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
        return self::getContainer()->getByType(ConstraintMustHaveAttributeRule::class);
    }
}
