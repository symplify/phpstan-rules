<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoRouteTrailingSlashPathRule;

use Iterator;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoRouteTrailingSlashPathRule;

final class NoRouteTrailingSlashPathRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SoleSlashController.php', []];
        yield [__DIR__ . '/Fixture/CorrectController.php', []];

        yield [
            __DIR__ . '/Fixture/InvalidController.php',
            [[sprintf(NoRouteTrailingSlashPathRule::ERROR_MESSAGE, '/some-route/'), 15]],
        ];

        yield [
            __DIR__ . '/Fixture/InvalidWithNameController.php',
            [[sprintf(NoRouteTrailingSlashPathRule::ERROR_MESSAGE, '/next-route/'), 15]],
        ];

        yield [
            __DIR__ . '/Fixture/PathAwareInvalidController.php',
            [[sprintf(NoRouteTrailingSlashPathRule::ERROR_MESSAGE, '/another-route/'), 15]],
        ];
    }

    protected function getRule(): NoRouteTrailingSlashPathRule
    {
        return new NoRouteTrailingSlashPathRule();
    }
}
