<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoRequiredOutsideClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\Handyman\PHPStan\Rule\NoRequiredOutsideClassRule;

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

    protected function getRule(): Rule
    {
        return new NoRequiredOutsideClassRule();
    }
}
