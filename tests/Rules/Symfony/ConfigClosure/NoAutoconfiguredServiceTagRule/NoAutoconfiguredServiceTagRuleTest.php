<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoAutoconfiguredServiceTagRule;

use Iterator;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\NoAutoconfiguredServiceTagRule;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoAutoconfiguredServiceTagRule\Source\SomeSubscriber;

final class NoAutoconfiguredServiceTagRuleTest extends RuleTestCase
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
            NoAutoconfiguredServiceTagRule::ERROR_MESSAGE,
            'kernel.event_subscriber',
            SomeSubscriber::class,
            EventSubscriberInterface::class
        );
        yield [__DIR__ . '/Fixture/RedundantTagConfig.php', [[$errorMessage, 15]]];

        yield [__DIR__ . '/Fixture/SkipNoAutoconfigureConfig.php', []];
        yield [__DIR__ . '/Fixture/SkipTagWithAttributesConfig.php', []];
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
        return self::getContainer()->getByType(NoAutoconfiguredServiceTagRule::class);
    }
}
