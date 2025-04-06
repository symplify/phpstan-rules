<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoDuplicateArgsAutowireByTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\NoDuplicateArgsAutowireByTypeRule;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoDuplicateArgAutowireByTypeRule\Source\AnotherType;

final class NoDuplicateArgsAutowireByTypeRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ConfigWithArgs.php', [[
            sprintf(NoDuplicateArgsAutowireByTypeRule::ERROR_MESSAGE, AnotherType::class),
            14,
        ]]];

        yield [__DIR__ . '/Fixture/SkipConfigWithDifferentArgs.php', []];
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoDuplicateArgsAutowireByTypeRule::class);
    }
}
