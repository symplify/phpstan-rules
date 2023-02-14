<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisCallOnLocalMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\RequireThisCallOnLocalMethodRule;

/**
 * @extends RuleTestCase<RequireThisCallOnLocalMethodRule>
 */
final class RequireThisCallOnLocalMethodRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCallParentMethodStatically.php', []];
        yield [__DIR__ . '/Fixture/SkipCallLocalStaticMethod.php', []];
        yield [__DIR__ . '/Fixture/CallLocalMethod.php', [[RequireThisCallOnLocalMethodRule::ERROR_MESSAGE, 11]]];
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
        return self::getContainer()->getByType(RequireThisCallOnLocalMethodRule::class);
    }
}
