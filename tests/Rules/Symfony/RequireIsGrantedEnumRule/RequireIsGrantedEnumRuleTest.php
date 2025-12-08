<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireIsGrantedEnumRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\RequireIsGrantedEnumRule;

final class RequireIsGrantedEnumRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipConstantResource.php', []];

        yield [__DIR__ . '/Fixture/SomeControllerWithStringyAttribute.php', [
            [sprintf(RequireIsGrantedEnumRule::ERROR_MESSAGE, 'some_resource'), 9],
        ]];
    }

    protected function getRule(): Rule
    {
        return new RequireIsGrantedEnumRule();
    }
}
