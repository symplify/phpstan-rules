<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequiredAbstractClassKeywordRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\RequiredAbstractClassKeywordRule;

/**
 * @extends RuleTestCase<RequiredAbstractClassKeywordRule>
 */
final class RequiredAbstractClassKeywordRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/AbstractClass.php', []];
        yield [__DIR__ . '/Fixture/SkipSomeClass.php', []];

        yield [
            __DIR__ . '/Fixture/AbstractPrefixOnNonAbstractClass.php',
            [[RequiredAbstractClassKeywordRule::ERROR_MESSAGE, 7]],
        ];
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
        return self::getContainer()->getByType(RequiredAbstractClassKeywordRule::class);
    }
}
