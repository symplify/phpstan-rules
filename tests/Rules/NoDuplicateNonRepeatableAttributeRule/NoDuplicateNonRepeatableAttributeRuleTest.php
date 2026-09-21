<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDuplicateNonRepeatableAttributeRule;

use Iterator;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoDuplicateNonRepeatableAttributeRule;
use Symplify\PHPStanRules\Tests\Rules\NoDuplicateNonRepeatableAttributeRule\Source\SingleAttribute;

/**
 * @extends RuleTestCase<NoDuplicateNonRepeatableAttributeRule>
 */
final class NoDuplicateNonRepeatableAttributeRuleTest extends RuleTestCase
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
            NoDuplicateNonRepeatableAttributeRule::ERROR_MESSAGE,
            SingleAttribute::class,
            2,
            'property'
        );
        yield [__DIR__ . '/Fixture/ReportDuplicateAttribute.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipRepeatableAttribute.php', []];
        yield [__DIR__ . '/Fixture/SkipSingleAttribute.php', []];
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
        return self::getContainer()->getByType(NoDuplicateNonRepeatableAttributeRule::class);
    }
}
