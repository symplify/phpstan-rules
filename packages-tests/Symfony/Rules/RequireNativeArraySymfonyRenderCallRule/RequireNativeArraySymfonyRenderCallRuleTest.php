<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\RequireNativeArraySymfonyRenderCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Symfony\Rules\RequireNativeArraySymfonyRenderCallRule;

final class RequireNativeArraySymfonyRenderCallRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNoSecondArgument.php', []];
        yield [__DIR__ . '/Fixture/SkipCorrectControllerRender.php', []];

        yield [
            __DIR__ . '/Fixture/ParameterAsSecondArgument.php',
            [[RequireNativeArraySymfonyRenderCallRule::ERROR_MESSAGE, 17]], ];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(RequireNativeArraySymfonyRenderCallRule::class);
    }
}
