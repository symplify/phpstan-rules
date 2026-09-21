<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\PreferInterfaceInConstructorRule;

use Iterator;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symplify\PHPStanRules\Rules\Symfony\PreferInterfaceInConstructorRule;

/**
 * @extends RuleTestCase<PreferInterfaceInConstructorRule>
 */
final class PreferInterfaceInConstructorRuleTest extends RuleTestCase
{
    /**
     * @param list<array{0: string, 1: int}> $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        $errorMessage = sprintf(
            PreferInterfaceInConstructorRule::ERROR_MESSAGE,
            '$router',
            Router::class,
            RouterInterface::class
        );
        yield [__DIR__ . '/Fixture/ReportConcreteRouter.php', [[$errorMessage, 12]]];

        yield [__DIR__ . '/Fixture/SkipRouterInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipProjectClass.php', []];
    }

    /**
     * @return string[]
     */
    #[Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(PreferInterfaceInConstructorRule::class);
    }
}
