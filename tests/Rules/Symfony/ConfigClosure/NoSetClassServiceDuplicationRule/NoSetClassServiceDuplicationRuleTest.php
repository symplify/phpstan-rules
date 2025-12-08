<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoSetClassServiceDuplicationRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\NoSetClassServiceDuplicationRule;

final class NoSetClassServiceDuplicationRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SetAndClassConfig.php', [
            [
                sprintf(NoSetClassServiceDuplicationRule::ERROR_MESSAGE, 'SomeClassToBeSet::class', 'SomeClassToBeSet::class', 'SomeClassToBeSet::class'),
                11,
            ],
        ]];

        yield [__DIR__ . '/Fixture/SkipDifferentSetAndClass.php', []];
        yield [__DIR__ . '/Fixture/SkipSoleSet.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoSetClassServiceDuplicationRule();
    }
}
