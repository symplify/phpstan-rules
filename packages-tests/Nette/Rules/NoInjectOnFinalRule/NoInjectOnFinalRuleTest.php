<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoInjectOnFinalRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Nette\Rules\NoInjectOnFinalRule;

final class NoInjectOnFinalRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipInjectOnNonAbstract.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractClass.php', []];

        yield [
            __DIR__ . '/Fixture/InjectOnNonAbstractWithAbstractParent.php',
            [[NoInjectOnFinalRule::ERROR_MESSAGE, 15]],
        ];

        yield [__DIR__ . '/Fixture/InjectAttributeWithParent.php', [[NoInjectOnFinalRule::ERROR_MESSAGE, 15]]];
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
        return self::getContainer()->getByType(NoInjectOnFinalRule::class);
    }
}
