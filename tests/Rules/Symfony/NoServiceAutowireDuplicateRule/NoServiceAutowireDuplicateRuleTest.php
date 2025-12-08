<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceAutowireDuplicateRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoServiceAutowireDuplicateRule;

final class NoServiceAutowireDuplicateRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/DuplicatedAutowire.php', [[
            NoServiceAutowireDuplicateRule::ERROR_MESSAGE,
            14,
        ]]];

        yield [__DIR__ . '/Fixture/NoDuplicate.php', []];
        yield [__DIR__ . '/Fixture/AlsoNoDuplicate.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoServiceAutowireDuplicateRule();
    }
}
