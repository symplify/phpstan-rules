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
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeActionInjectionController.php', [[
            sprintf(NoControllerMethodInjectionRule::ERROR_MESSAGE, SomeService::class),
            10,
        ]]];

        yield [__DIR__ . '/Fixture/SkipRequestParameterController.php', []];
        yield [__DIR__ . '/Fixture/SkipScalarParameterController.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoControllerMethodInjectionRule();
    }
}
