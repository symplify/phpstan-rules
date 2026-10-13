<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionReturnMustHaveGenericDocblockRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\CollectionReturnMustHaveGenericDocblockRule;

/**
 * @extends RuleTestCase<CollectionReturnMustHaveGenericDocblockRule>
 */
final class CollectionReturnMustHaveGenericDocblockRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(CollectionReturnMustHaveGenericDocblockRule::ERROR_MESSAGE, 'getItems');

        yield [__DIR__ . '/Fixture/ReportCollectionReturn.php', [[$errorMessage, 11]]];
        yield [__DIR__ . '/Fixture/SkipCollectionReturn.php', []];
    }

    protected function getRule(): Rule
    {
        return new CollectionReturnMustHaveGenericDocblockRule();
    }
}
