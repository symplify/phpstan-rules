<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReadonlyApiParamRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoReadonlyApiParamRule;

/**
 * @extends RuleTestCase<NoReadonlyApiParamRule>
 */
final class NoReadonlyApiParamRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(NoReadonlyApiParamRule::ERROR_MESSAGE, 'service');

        yield [__DIR__ . '/Fixture/ReportReadonlyApiParam.php', [[$errorMessage, 13]]];
        yield [__DIR__ . '/Fixture/SkipReadonlyApiParam.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoReadonlyApiParamRule();
    }
}
