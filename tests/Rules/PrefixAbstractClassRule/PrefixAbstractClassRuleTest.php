<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PrefixAbstractClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PrefixAbstractClassRule;

final class PrefixAbstractClassRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAnonymousClass.php', []];
        yield [__DIR__ . '/Fixture/SkipInterface.php', []];
        yield [__DIR__ . '/Fixture/AbstractSomeAbstractClass.php', []];

        $errorMessage = sprintf(PrefixAbstractClassRule::ERROR_MESSAGE, 'SomeAbstractClass');
        yield [__DIR__ . '/Fixture/SomeAbstractClass.php', [[$errorMessage, 7]]];
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
        return self::getContainer()->getByType(PrefixAbstractClassRule::class);
    }
}
