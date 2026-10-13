<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionPropertyMustHaveGenericDocblockRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\CollectionPropertyMustHaveGenericDocblockRule;

/**
 * @extends RuleTestCase<CollectionPropertyMustHaveGenericDocblockRule>
 */
final class CollectionPropertyMustHaveGenericDocblockRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(CollectionPropertyMustHaveGenericDocblockRule::ERROR_MESSAGE, 'items');

        yield [__DIR__ . '/Fixture/ReportCollectionProperty.php', [[$errorMessage, 11]]];
        yield [__DIR__ . '/Fixture/SkipCollectionProperty.php', []];
    }

    protected function getRule(): Rule
    {
        return new CollectionPropertyMustHaveGenericDocblockRule();
    }
}
