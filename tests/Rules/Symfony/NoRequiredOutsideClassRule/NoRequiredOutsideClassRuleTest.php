<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoRequiredOutsideClassRule;

use Iterator;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoRequiredOutsideClassRule;

final class NoRequiredOutsideClassRuleTest extends RuleTestCase
{
    /**
     * @param string[] $filePaths
     */
    #[DataProvider('provideData')]
    public function testRule(array $filePaths, array $expectedErrorsWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [[
            __DIR__ . '/Fixture/SomeClassUsingTrait.php',
            __DIR__ . '/Fixture/TraitWithRequireAttribute.php',
            __DIR__ . '/Fixture/TraitWithRequire.php',
        ], [[NoRequiredOutsideClassRule::ERROR_MESSAGE, 9], [NoRequiredOutsideClassRule::ERROR_MESSAGE, 10]]];
    }

    protected function getRule(): NoRequiredOutsideClassRule
    {
        return new NoRequiredOutsideClassRule();
    }
}
