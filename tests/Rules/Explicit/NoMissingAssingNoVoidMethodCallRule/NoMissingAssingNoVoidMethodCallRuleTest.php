<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule;

final class NoMissingAssingNoVoidMethodCallRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/ReturnedNoVoid.php', [[NoMissingAssingNoVoidMethodCallRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipTokens.php', []];
        yield [__DIR__ . '/Fixture/SkipReturnedNoVoid.php', []];
        yield [__DIR__ . '/Fixture/SkipFluentOutsideOnPurpose.php', []];
        yield [__DIR__ . '/Fixture/SkipSymfonyContainerConfigurator.php', []];
        yield [__DIR__ . '/Fixture/SkipDefaultSymfonyAutowire.php', []];
        yield [__DIR__ . '/Fixture/SkipNodeTraverser.php', []];
        yield [__DIR__ . '/Fixture/SkipCommandOptions.php', []];
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
        return self::getContainer()->getByType(NoMissingAssingNoVoidMethodCallRule::class);
    }
}
