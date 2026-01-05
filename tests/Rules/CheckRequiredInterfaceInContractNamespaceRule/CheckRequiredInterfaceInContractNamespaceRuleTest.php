<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredInterfaceInContractNamespaceRule;

use Iterator;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\CheckRequiredInterfaceInContractNamespaceRule;

final class CheckRequiredInterfaceInContractNamespaceRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/Contract/SkipInterfaceInContract.php', []];
        yield [__DIR__ . '/Fixture/Illuminate/Contracts/View/View.php', []];
        yield [
            __DIR__ . '/Fixture/AnInterfaceNotInContract.php',
            [[CheckRequiredInterfaceInContractNamespaceRule::ERROR_MESSAGE, 7]], ];
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
        return self::getContainer()->getByType(CheckRequiredInterfaceInContractNamespaceRule::class);
    }
}
