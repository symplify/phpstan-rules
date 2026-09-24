<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoFindTaggedServiceIdsCallRule;

use Iterator;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoFindTaggedServiceIdsCallRule;

/**
 * @extends RuleTestCase<NoFindTaggedServiceIdsCallRule>
 */
final class NoFindTaggedServiceIdsCallRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/FindTaggedServiceIdsInPass.php', [[NoFindTaggedServiceIdsCallRule::ERROR_MESSAGE, 14]]];

        yield [__DIR__ . '/Fixture/FindTaggedServiceIdsSimpleForeach.php', [[NoFindTaggedServiceIdsCallRule::ERROR_MESSAGE, 16]]];

        yield [__DIR__ . '/Fixture/SkipTagAttributesInForeach.php', []];

        yield [__DIR__ . '/Fixture/SkipFindTaggedServiceIdsInContext.php', []];
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
        return self::getContainer()->getByType(NoFindTaggedServiceIdsCallRule::class);
    }
}
