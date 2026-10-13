<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionParamMustHaveGenericDocblockRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Doctrine\CollectionParamMustHaveGenericDocblockRule;

/**
 * @extends RuleTestCase<CollectionParamMustHaveGenericDocblockRule>
 */
final class CollectionParamMustHaveGenericDocblockRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(CollectionParamMustHaveGenericDocblockRule::ERROR_MESSAGE, 'items', 'setItems', 'items');

        yield [__DIR__ . '/Fixture/ReportCollectionParam.php', [[$errorMessage, 11]]];
        yield [__DIR__ . '/Fixture/SkipCollectionParam.php', []];
    }

    protected function getRule(): Rule
    {
        return new CollectionParamMustHaveGenericDocblockRule();
    }
}
