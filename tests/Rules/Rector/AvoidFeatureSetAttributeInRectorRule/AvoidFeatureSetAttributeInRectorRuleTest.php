<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\AvoidFeatureSetAttributeInRectorRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Rector\AvoidFeatureSetAttributeInRectorRule;

final class AvoidFeatureSetAttributeInRectorRuleTest extends RuleTestCase
{
    /**
     * @param array<int, array<string|int>> $expectedErrorsWithLines
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
        yield [__DIR__ . '/Fixture/SetLocalAttribute.php', [[
            sprintf(AvoidFeatureSetAttributeInRectorRule::ERROR_MESSAGE, 'some_attribute'), 11,
        ]]];

        yield [__DIR__ . '/Fixture/SkipAllowedSetAttributesNode.php', []];
    }

    protected function getRule(): Rule
    {
        return new AvoidFeatureSetAttributeInRectorRule();
    }
}
