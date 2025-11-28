<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoBareAndSecurityIsGrantedContentsRule;

use Iterator;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoBareAndSecurityIsGrantedContentsRule;

final class NoBareAndSecurityIsGrantedContentsRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeControllerWithComplexAttribute.php', [
            [NoBareAndSecurityIsGrantedContentsRule::ERROR_MESSAGE, 9],
        ]];

        yield [__DIR__ . '/Fixture/SomeControllerWithAmpersand.php', [
            [NoBareAndSecurityIsGrantedContentsRule::ERROR_MESSAGE, 9],
        ]];

        yield [__DIR__ . '/Fixture/SkipInnerOr.php', []];
        yield [__DIR__ . '/Fixture/SkipCustomFunction.php', []];
        yield [__DIR__ . '/Fixture/SkipSplitOne.php', []];
    }

    protected function getRule(): NoBareAndSecurityIsGrantedContentsRule
    {
        return new NoBareAndSecurityIsGrantedContentsRule();
    }
}
