<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\RequireExceptionNamespaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Domain\RequireExceptionNamespaceRule;

final class RequireExceptionNamespaceRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/MisslocatedException.php', [[RequireExceptionNamespaceRule::ERROR_MESSAGE, 9]]];
        yield [__DIR__ . '/Fixture/Exception/SkipCorrectException.php', []];
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
        return self::getContainer()->getByType(RequireExceptionNamespaceRule::class);
    }
}
