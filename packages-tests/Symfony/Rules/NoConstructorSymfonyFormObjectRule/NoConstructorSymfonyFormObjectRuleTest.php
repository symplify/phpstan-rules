<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\NoConstructorSymfonyFormObjectRule;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\TestRunner\TestResult\Collector;
use Symplify\PHPStanRules\Collector\ClassMethod\FormTypeClassCollector;
use Symplify\PHPStanRules\Symfony\Rules\NoConstructorSymfonyFormObjectRule;

/**
 * @extends RuleTestCase<NoConstructorSymfonyFormObjectRule>
 */
final class NoConstructorSymfonyFormObjectRuleTest extends RuleTestCase
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
        yield [
            [
                __DIR__ . '/Fixture/SkipClassUsedInSymfonyFormWithoutConstructor.php',
                __DIR__ . '/Source/SymfonyFormUsingFirstObject.php',
            ],
            [],
        ];

        yield [
            [
                __DIR__ . '/Fixture/ClassUsedInSymfonyFormButWithConstructor.php',
                __DIR__ . '/Source/SymfonyFormUsingSecondObject.php',
            ],
            [[NoConstructorSymfonyFormObjectRule::ERROR_MESSAGE, 6]],
        ];

        yield [
            [
                __DIR__ . '/Fixture/ClassUsedInSymfonyFormButWithConstructor.php',
                __DIR__ . '/Source/SymfonyFormSetDefault.php',
            ],
            [[NoConstructorSymfonyFormObjectRule::ERROR_MESSAGE, 6]],
        ];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    /**
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        $formTypeClassCollector = self::getContainer()->getByType(FormTypeClassCollector::class);

        return [$formTypeClassCollector];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoConstructorSymfonyFormObjectRule::class);
    }
}
