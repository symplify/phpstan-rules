<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NullablePropertyGetterMustReturnNullableRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NullablePropertyGetterMustReturnNullableRule;

/**
 * @extends RuleTestCase<NullablePropertyGetterMustReturnNullableRule>
 */
final class NullablePropertyGetterMustReturnNullableRuleTest extends RuleTestCase
{
    /**
     * @param list<array{0: string, 1: int}> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        $errorMessage = sprintf(NullablePropertyGetterMustReturnNullableRule::ERROR_MESSAGE, 'getName', 'name');

        yield [__DIR__ . '/Fixture/ReportNullableGetter.php', [[$errorMessage, 11]]];
        yield [__DIR__ . '/Fixture/SkipNullableGetter.php', []];
    }

    protected function getRule(): Rule
    {
        return new NullablePropertyGetterMustReturnNullableRule();
    }
}
