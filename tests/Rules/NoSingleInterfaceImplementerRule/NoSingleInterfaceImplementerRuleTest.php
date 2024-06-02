<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoSingleInterfaceImplementerRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Collector\ImplementedInterfaceCollector;
use Symplify\PHPStanRules\Collector\InterfaceCollector;
use Symplify\PHPStanRules\Collector\InterfaceOfAbstractClassCollector;
use Symplify\PHPStanRules\Rules\NoSingleInterfaceImplementerRule;
use Symplify\PHPStanRules\Tests\Rules\NoSingleInterfaceImplementerRule\Fixture\SimpleInterface;

/**
 * @extends RuleTestCase<NoSingleInterfaceImplementerRule>
 */
final class NoSingleInterfaceImplementerRuleTest extends RuleTestCase
{
    /**
     * @param string[] $filePaths
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SimpleInterface.php'], []];
        yield [[__DIR__ . '/Fixture/AllowAbstract.php', __DIR__ . '/Fixture/SimpleInterface.php'], []];

        // already counted in abstract class
        yield [[__DIR__ . '/Fixture/AllowAbstract.php', __DIR__ . '/Fixture/SimpleInterface.php', __DIR__ . '/Fixture/ImplementsSimpleInterface.php'], []];

        yield [
            [
                __DIR__ . '/Fixture/SimpleInterface.php',
                __DIR__ . '/Fixture/ImplementsSimpleInterface.php',
            ], [
                [
                    sprintf(NoSingleInterfaceImplementerRule::ERROR_MESSAGE, SimpleInterface::class),
                    -1,
                ],
            ]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getCollectors(): array
    {
        return [
            self::getContainer()->getByType(ImplementedInterfaceCollector::class),
            self::getContainer()->getByType(InterfaceCollector::class),
            self::getContainer()->getByType(InterfaceOfAbstractClassCollector::class),
        ];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoSingleInterfaceImplementerRule::class);
    }
}
