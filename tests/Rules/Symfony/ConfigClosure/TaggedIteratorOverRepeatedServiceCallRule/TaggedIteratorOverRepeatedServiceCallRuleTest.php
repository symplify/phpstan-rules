<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\TaggedIteratorOverRepeatedServiceCallRule;

use Iterator;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\TaggedIteratorOverRepeatedServiceCallRule;

final class TaggedIteratorOverRepeatedServiceCallRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ConfigWithRepeatedCalls.php', [
            [
                sprintf(TaggedIteratorOverRepeatedServiceCallRule::ERROR_MESSAGE, 'repeatedMethod'),
                11,
            ],
        ]];

        yield [__DIR__ . '/Fixture/SkipOnlyFewRepeats.php', []];
        yield [__DIR__ . '/Fixture/SkipNonServiceCalls.php', []];
    }

    protected function getRule(): TaggedIteratorOverRepeatedServiceCallRule
    {
        return new TaggedIteratorOverRepeatedServiceCallRule();
    }
}
