<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSetterCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\NoServiceSetterCallRule;

final class NoServiceSetterCallRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(NoServiceSetterCallRule::ERROR_MESSAGE, 'setSomeService');
        yield [__DIR__ . '/Fixture/SetterCallConfig.php', [[$errorMessage, 13]]];

        yield [__DIR__ . '/Fixture/SkipNonSetterAndParamConfig.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoServiceSetterCallRule();
    }
}
