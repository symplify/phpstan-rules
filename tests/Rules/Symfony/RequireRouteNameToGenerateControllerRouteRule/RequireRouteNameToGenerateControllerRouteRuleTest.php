<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule;

use Iterator;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule;

final class RequireRouteNameToGenerateControllerRouteRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/CallingCorrectController.php', []];
        yield [__DIR__ . '/Fixture/TwoRoutes.php', []];

        yield [__DIR__ . '/Fixture/CallingWrongController.php', [
            [RequireRouteNameToGenerateControllerRouteRule::ERROR_MESSAGE, 14],
        ]];

        yield [__DIR__ . '/Fixture/CallingControllerWithoutInvoke.php', [
            [RequireRouteNameToGenerateControllerRouteRule::ERROR_MESSAGE, 14],
        ]];

        yield [__DIR__ . '/Fixture/CallingControllerWithWrongString.php', [
            [RequireRouteNameToGenerateControllerRouteRule::ERROR_MESSAGE, 14],
        ]];
    }

    protected function getRule(): Rule
    {
        $reflectionProvider = self::getContainer()->getByType(ReflectionProvider::class);

        return new RequireRouteNameToGenerateControllerRouteRule($reflectionProvider);
    }
}
