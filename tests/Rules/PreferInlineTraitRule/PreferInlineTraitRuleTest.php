<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferInlineTraitRule;

use Iterator;
use Override;
use PhpParser\Node;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Collector\TraitDefinitionCollector;
use Symplify\PHPStanRules\Collector\TraitUsageCollector;
use Symplify\PHPStanRules\Rules\PreferInlineTraitRule;
use Symplify\PHPStanRules\Tests\Rules\PreferInlineTraitRule\Fixture\InlineCandidateTrait;

final class PreferInlineTraitRuleTest extends RuleTestCase
{
    /**
     * @param string[] $filePaths
     * @param array<int, array<string|int>> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array{string[], array<array{string, int}>}>
     */
    public static function provideData(): Iterator
    {
        $lazyTraitErrorMessage = sprintf(
            PreferInlineTraitRule::ERROR_MESSAGE,
            InlineCandidateTrait::class,
            6,
            1,
            PHP_EOL
        );

        yield 'used once, inline candidate' => [
            [__DIR__ . '/Fixture/InlineCandidateTrait.php', __DIR__ . '/Fixture/SingleTraitUser.php'],
            [[$lazyTraitErrorMessage, 7]],
        ];

        yield 'used more than max, skip' => [
            [
                __DIR__ . '/Fixture/PopularTrait.php',
                __DIR__ . '/Fixture/PopularTraitFirstUser.php',
                __DIR__ . '/Fixture/PopularTraitSecondUser.php',
                __DIR__ . '/Fixture/PopularTraitThirdUser.php',
            ],
            [],
        ];
    }

    /**
     * @return string[]
     */
    #[Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(PreferInlineTraitRule::class);
    }

    /**
     * @return list<Collector<Node, mixed>>
     */
    #[Override]
    protected function getCollectors(): array
    {
        return [
            self::getContainer()->getByType(TraitDefinitionCollector::class),
            self::getContainer()->getByType(TraitUsageCollector::class),
        ];
    }
}
