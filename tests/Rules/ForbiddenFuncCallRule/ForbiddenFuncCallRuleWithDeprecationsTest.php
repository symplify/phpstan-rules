<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenFuncCallRule;

use Iterator;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule;

final class ForbiddenFuncCallRuleWithDeprecationsTest extends RuleTestCase
{
    /**
     * @param array<int, array<string|int>> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        // custom messages are defined in the config file

        $errorMessage = sprintf(ForbiddenFuncCallRule::ERROR_MESSAGE, 'dump');
        $errorMessage .= ': seems you missed some debugging function';
        yield [__DIR__ . '/Fixture/DebugFuncCall.php', [[$errorMessage, 11]]];

        $errorMessage = sprintf(ForbiddenFuncCallRule::ERROR_MESSAGE, 'extract');
        $errorMessage .= ': you shouldn"t use this dynamic things';
        yield [__DIR__ . '/Fixture/ExtractCall.php', [[$errorMessage, 11]]];

        // custom error defined as empty-string -> just prints the default message
        $errorMessage = sprintf(ForbiddenFuncCallRule::ERROR_MESSAGE, 'property_exists');
        yield [__DIR__ . '/Fixture/PropertyExists.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipPropertyExistsOnXml.php', []];
    }

    /**
     * @return string[]
     */
    #[Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule_with_deprecations.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(ForbiddenFuncCallRule::class);
    }
}
