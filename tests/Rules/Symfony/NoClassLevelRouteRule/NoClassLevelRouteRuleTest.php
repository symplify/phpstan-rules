<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoClassLevelRouteRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoClassLevelRouteRule;

final class NoClassLevelRouteRuleTest extends RuleTestCase
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
            __DIR__ . '/Fixture/SkipControllerWithMethodRoute.php',
        ], []];

        yield [[
            __DIR__ . '/Fixture/ControllerWithClassAttributeRoute.php',
        ], [[NoClassLevelRouteRule::ERROR_MESSAGE, 8]]];

        yield [[
            __DIR__ . '/Fixture/ControllerWithClassRoute.php',
        ], [[NoClassLevelRouteRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return new NoClassLevelRouteRule();
    }
}
