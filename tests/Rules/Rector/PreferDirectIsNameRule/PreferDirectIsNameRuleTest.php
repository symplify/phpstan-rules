<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\PreferDirectIsNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Rector\PreferDirectIsNameRule;

final class PreferDirectIsNameRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipDirectIsName.php', []];

        yield [__DIR__ . '/Fixture/NonDirectIsName.php', [
            [
                PreferDirectIsNameRule::ERROR_MESSAGE,
                19,
            ],
        ]];
    }

    protected function getRule(): Rule
    {
        return new PreferDirectIsNameRule();
    }
}
