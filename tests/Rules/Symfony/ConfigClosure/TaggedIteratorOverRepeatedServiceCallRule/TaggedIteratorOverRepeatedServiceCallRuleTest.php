<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\TaggedIteratorOverRepeatedServiceCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\TaggedIteratorOverRepeatedServiceCallRule;

final class TaggedIteratorOverRepeatedServiceCallRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ConfigWithRepeatedCalls.php', [
            [
                sprintf(TaggedIteratorOverRepeatedServiceCallRule::ERROR_MESSAGE, 'repeatedMethod'),
                11,
            ],
        ]];

        yield [__DIR__ . '/Fixture/SkipOnlyFewRepeats.php', []];
        yield [__DIR__ . '/Fixture/SkipNonServiceCalls.php', []];
    }

    protected function getRule(): Rule
    {
        return new TaggedIteratorOverRepeatedServiceCallRule();
    }
}
