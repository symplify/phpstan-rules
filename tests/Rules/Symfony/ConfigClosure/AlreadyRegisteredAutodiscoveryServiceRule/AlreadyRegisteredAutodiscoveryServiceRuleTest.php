<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\AlreadyRegisteredAutodiscoveryServiceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\ConfigClosure\AlreadyRegisteredAutodiscoveryServiceRule;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\AlreadyRegisteredAutodiscoveryServiceRule\Source\RegisterAsService;

final class AlreadyRegisteredAutodiscoveryServiceRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/DuplicatedServiceRegistration.php', [[
            sprintf(AlreadyRegisteredAutodiscoveryServiceRule::ERROR_MESSAGE, RegisterAsService::class),
            13,
        ]]];

        yield [__DIR__ . '/Fixture/SkipExcludedPath.php', []];
        yield [__DIR__ . '/Fixture/SkipDifferentLoad.php', []];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/config/configured_rule.neon',
        ];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(AlreadyRegisteredAutodiscoveryServiceRule::class);
    }
}
