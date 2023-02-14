<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\ObjectCalisthenics\Rules\NoShortNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoShortNameRule;

final class NoShortNameRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipId.php', []];

        $errorMessage = sprintf(NoShortNameRule::ERROR_MESSAGE, 'em', 3);
        $yeErrorMEssage = sprintf(NoShortNameRule::ERROR_MESSAGE, 'YE', 3);
        yield [__DIR__ . '/Fixture/ShortNamingClass.php', [[$errorMessage, 9], [$yeErrorMEssage, 11]]];

        $errorMessage = sprintf(NoShortNameRule::ERROR_MESSAGE, 'n', 3);
        yield [__DIR__ . '/Fixture/ShortClosureParam.php', [[$errorMessage, 11]]];
        yield [__DIR__ . '/Fixture/ShortParam.php', [[$errorMessage, 9]]];

        $errorMessage = sprintf(NoShortNameRule::ERROR_MESSAGE, 'n', 3);
        yield [
            __DIR__ . '/Fixture/ShortAssignParameter.php',
            [[$errorMessage, 11], [$errorMessage, 13], [$errorMessage, 15]], ];
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
        return self::getContainer()->getByType(NoShortNameRule::class);
    }
}
