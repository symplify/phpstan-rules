<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoControllerMethodInjectionRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoControllerMethodInjectionRule;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoControllerMethodInjectionRule\Source\SomeService;

final class NoControllerMethodInjectionRuleTest extends RuleTestCase
{
    /**
     * @param array<int, array<string|int>> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeActionInjectionController.php', [[
            sprintf(NoControllerMethodInjectionRule::ERROR_MESSAGE, SomeService::class),
            15,
        ]]];

        yield [__DIR__ . '/Fixture/InvokableActionInjectionController.php', [[
            sprintf(NoControllerMethodInjectionRule::ERROR_MESSAGE, SomeService::class),
            15,
        ]]];

        yield [__DIR__ . '/Fixture/SkipRequestParameterController.php', []];
        yield [__DIR__ . '/Fixture/SkipScalarParameterController.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoControllerMethodInjectionRule();
    }
}
