<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ClassNameRespectsParentSuffixRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\ClassNameRespectsParentSuffixRule;

final class ClassNameRespectsParentSuffixRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipSomeEventSubscriber.php', []];
        yield [__DIR__ . '/Fixture/SkipFixer.php', []];
        yield [__DIR__ . '/Fixture/SkipAnonymousClass.php', []];
        yield [__DIR__ . '/Fixture/SkipTest.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractTestCase.php', []];

        $errorMessage = sprintf(ClassNameRespectsParentSuffixRule::ERROR_MESSAGE, 'Test');
        yield [__DIR__ . '/Fixture/NonTestSuffix.php', [[$errorMessage, 9]]];

        $errorMessage = sprintf(ClassNameRespectsParentSuffixRule::ERROR_MESSAGE, 'Command');
        yield [__DIR__ . '/Fixture/SomeController.php', [[$errorMessage, 9]]];

        $errorMessage = sprintf(ClassNameRespectsParentSuffixRule::ERROR_MESSAGE, 'EventSubscriber');
        yield [__DIR__ . '/Fixture/SomeEventSubscriberFalse.php', [[$errorMessage, 9]]];
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
        return self::getContainer()->getByType(ClassNameRespectsParentSuffixRule::class);
    }
}
