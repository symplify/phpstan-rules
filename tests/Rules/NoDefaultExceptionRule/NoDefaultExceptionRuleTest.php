<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDefaultExceptionRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Symplify\PHPStanRules\Rules\NoDefaultExceptionRule;

final class NoDefaultExceptionRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<string|int[]|string[]>>
     */
    public static function provideData(): Iterator
    {
        $errorMessage = sprintf(NoDefaultExceptionRule::ERROR_MESSAGE, RuntimeException::class);
        yield [__DIR__ . '/Fixture/ThrowGenericException.php', [[$errorMessage, 13]]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoDefaultExceptionRule::class);
    }
}
