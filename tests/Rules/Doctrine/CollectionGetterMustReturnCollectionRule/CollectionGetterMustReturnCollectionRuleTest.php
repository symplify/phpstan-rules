<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionGetterMustReturnCollectionRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\CollectionGetterMustReturnCollectionRule;

/**
 * @extends RuleTestCase<CollectionGetterMustReturnCollectionRule>
 */
final class CollectionGetterMustReturnCollectionRuleTest extends RuleTestCase
{
    /**
     * @param list<array{0: string, 1: int}> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        $errorMessage = sprintf(
            CollectionGetterMustReturnCollectionRule::ERROR_MESSAGE,
            'getItems',
            'items',
            'Doctrine\Common\Collections\Collection'
        );

        yield [__DIR__ . '/Fixture/ReportCollectionGetter.php', [[$errorMessage, 14]]];
        yield [__DIR__ . '/Fixture/SkipCollectionGetter.php', []];
    }

    protected function getRule(): Rule
    {
        return new CollectionGetterMustReturnCollectionRule();
    }
}
