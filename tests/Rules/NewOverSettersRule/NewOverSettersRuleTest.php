<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule;

use Iterator;
use Override;
use PhpParser\Node;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Collector\NewWithFollowingSettersCollector;
use Symplify\PHPStanRules\Rules\NewOverSettersRule;
use Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Source\SomeObject;

final class NewOverSettersRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(
            NewOverSettersRule::ERROR_MESSAGE,
            SomeObject::class,
            2,
            'setAge()", "setName',
            PHP_EOL
        );

        yield [__DIR__ . '/Fixture/AlwaysSetters.php', [[$errorMessage, -1]]];
        yield [__DIR__ . '/Fixture/AlwaysSettersWithDifferentOrder.php', [[$errorMessage, -1]]];
        yield [__DIR__ . '/Fixture/ThreeTimesAlwaysSetters.php', [[$errorMessage, -1]]];

        yield [__DIR__ . '/Fixture/SkipDifferentSingleMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipOnceThanTwiceMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipReturnInMiddle.php', []];
        yield [__DIR__ . '/Fixture/SkipCalledOnlyOnce.php', []];

        yield [__DIR__ . '/Fixture/SkipSomeKernel.php', []];
        yield [__DIR__ . '/Fixture/SkipEntity.php', []];
        yield [__DIR__ . '/Fixture/SkipNotSetterCall.php', []];
        yield [__DIR__ . '/Fixture/SkipNoArgSetters.php', []];
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
        return self::getContainer()->getByType(NewOverSettersRule::class);
    }

    /**
     * @return list<Collector<Node, mixed>>
     */
    #[Override]
    protected function getCollectors(): array
    {
        return [self::getContainer()->getByType(NewWithFollowingSettersCollector::class)];
    }
}
