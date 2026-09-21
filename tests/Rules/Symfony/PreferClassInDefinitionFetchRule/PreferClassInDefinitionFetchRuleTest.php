<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\PreferClassInDefinitionFetchRule;

use Iterator;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\PreferClassInDefinitionFetchRule;
use Symplify\PHPStanRules\Tests\Rules\Symfony\PreferClassInDefinitionFetchRule\Source\SomeHelper;

final class PreferClassInDefinitionFetchRuleTest extends RuleTestCase
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
        $className = SomeHelper::class;
        $errorMessage = sprintf(PreferClassInDefinitionFetchRule::ERROR_MESSAGE, $className, $className);
        yield [__DIR__ . '/Fixture/SomeCompilerPass.php', [[$errorMessage, 16]]];
    }

    /**
     * @return array<int, string>
     */
    #[Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(PreferClassInDefinitionFetchRule::class);
    }
}
