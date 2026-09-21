<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\PreferClassServiceReferenceRule;

use Iterator;
use Override;
use PhpParser\Node;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Collector\ClassTargetServiceAliasCollector;
use Symplify\PHPStanRules\Collector\ServiceStringReferenceCollector;
use Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\PreferClassServiceReferenceRule;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\PreferClassServiceReferenceRule\Source\SomeHelper;

final class PreferClassServiceReferenceRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(PreferClassServiceReferenceRule::ERROR_MESSAGE, SomeHelper::class, 'some.helper');
        yield [__DIR__ . '/Fixture/StringServiceReferenceConfig.php', [[$errorMessage, 15]]];

        yield [__DIR__ . '/Fixture/SkipClassServiceReferenceConfig.php', []];
        yield [__DIR__ . '/Fixture/SkipUnaliasedStringReferenceConfig.php', []];
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
        return self::getContainer()->getByType(PreferClassServiceReferenceRule::class);
    }

    /**
     * @return list<Collector<Node, mixed>>
     */
    #[Override]
    protected function getCollectors(): array
    {
        return [
            self::getContainer()->getByType(ClassTargetServiceAliasCollector::class),
            self::getContainer()->getByType(ServiceStringReferenceCollector::class),
        ];
    }
}
