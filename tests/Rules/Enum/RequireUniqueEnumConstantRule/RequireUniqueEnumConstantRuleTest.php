<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireUniqueEnumConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Enum\RequireUniqueEnumConstantRule;

final class RequireUniqueEnumConstantRuleTest extends RuleTestCase
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
        $expectedErrorMessage = sprintf(RequireUniqueEnumConstantRule::ERROR_MESSAGE, 'yes');
        yield [__DIR__ . '/Fixture/InvalidEnum.php', [[$expectedErrorMessage, 8]]];
        yield [__DIR__ . '/Fixture/InvalidAnnotationEnum.php', [[$expectedErrorMessage, 8]]];

        yield [__DIR__ . '/Fixture/SkipValidEnum.php', []];
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
        return self::getContainer()->getByType(RequireUniqueEnumConstantRule::class);
    }
}
