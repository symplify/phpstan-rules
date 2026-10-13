<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\MockedMethodReturnMustMatchNativeTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PHPUnit\MockedMethodReturnMustMatchNativeTypeRule;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\MockedMethodReturnMustMatchNativeTypeRule\Source\SomeObject;

/**
 * @extends RuleTestCase<MockedMethodReturnMustMatchNativeTypeRule>
 */
final class MockedMethodReturnMustMatchNativeTypeRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(
            MockedMethodReturnMustMatchNativeTypeRule::ERROR_MESSAGE,
            SomeObject::class,
            'getName',
            'string',
            'int'
        );

        yield [__DIR__ . '/Fixture/ReportMockedReturn.php', [[$errorMessage, 14]]];
        yield [__DIR__ . '/Fixture/SkipMockedReturn.php', []];
    }

    protected function getRule(): Rule
    {
        return new MockedMethodReturnMustMatchNativeTypeRule();
    }
}
