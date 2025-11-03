<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoRoutingPrefixRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoRoutingPrefixRule;

final class NoRoutingPrefixRuleTest extends RuleTestCase
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
            __DIR__ . '/Fixture/skip_no_route_prefix.php',
        ], []];

        yield [[
            __DIR__ . '/Fixture/skip_bundle_import.php',
        ], []];

        yield [[
            __DIR__ . '/Fixture/routing_imports.php',
        ], [[NoRoutingPrefixRule::ERROR_MESSAGE, 8]]];
    }

    protected function getRule(): Rule
    {
        return new NoRoutingPrefixRule();
    }
}
