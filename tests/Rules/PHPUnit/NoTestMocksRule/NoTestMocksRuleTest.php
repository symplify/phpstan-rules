<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoTestMocksRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PHPUnit\NoTestMocksRule;

final class NoTestMocksRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/SomeMocking.php',
            [[sprintf(NoTestMocksRule::ERROR_MESSAGE, 'SomeClass'), 13]],
        ];

        yield [__DIR__ . '/Fixture/SkipApiMock.php', []];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/config/configured_rule.neon',
        ];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoTestMocksRule::class);
    }
}
