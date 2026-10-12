<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\PreferGenericTemplateOnSameUnionTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Rector\PreferGenericTemplateOnSameUnionTypeRule;

final class PreferGenericTemplateOnSameUnionTypeRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(PreferGenericTemplateOnSameUnionTypeRule::ERROR_MESSAGE, 'ClassMethod|Function_');
        yield [__DIR__ . '/Fixture/SameUnionParamAndReturn.php', [[$errorMessage, 12]]];

        yield [__DIR__ . '/Fixture/SkipGenericTemplate.php', []];
    }

    protected function getRule(): Rule
    {
        return new PreferGenericTemplateOnSameUnionTypeRule();
    }
}
