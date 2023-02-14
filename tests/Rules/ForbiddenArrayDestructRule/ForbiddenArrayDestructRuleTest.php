<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayDestructRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\ForbiddenArrayDestructRule;

final class ForbiddenArrayDestructRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ClassWithArrayDestruct.php', [[ForbiddenArrayDestructRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/SkipSwap.php', []];
        yield [__DIR__ . '/Fixture/SkipExplode.php', []];
        yield [__DIR__ . '/Fixture/SkipStringsSplit.php', []];
        yield [__DIR__ . '/Fixture/SkipExternalReturnArray.php', []];
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
        return self::getContainer()->getByType(ForbiddenArrayDestructRule::class);
    }
}
