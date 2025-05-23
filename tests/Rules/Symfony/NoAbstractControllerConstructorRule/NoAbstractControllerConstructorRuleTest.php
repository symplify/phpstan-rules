<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoAbstractControllerConstructorRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoAbstractControllerConstructorRule;

final class NoAbstractControllerConstructorRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeAbstractController.php', [[
            NoAbstractControllerConstructorRule::ERROR_MESSAGE,
            7,
        ]]];

        yield [__DIR__ . '/Fixture/SkipNonAbstractController.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoAbstractControllerConstructorRule();
    }
}
