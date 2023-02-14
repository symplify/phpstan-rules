<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredClassRule;

use DateTime as NativeDateTime;
use Iterator;
use Nette\Utils\DateTime;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\PreferredClassRule;
use Symplify\PHPStanRules\Tests\Rules\PreferredClassRule\Fixture\SkipPreferredExtendingTheOldOne;
use Symplify\PHPStanRules\Tests\Rules\PreferredClassRule\Source\AbstractNotWhatYouWant;

final class PreferredClassRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(PreferredClassRule::ERROR_MESSAGE, NativeDateTime::class, DateTime::class);
        yield [__DIR__ . '/Fixture/ClassUsingOld.php', [[$errorMessage, 13]]];
        yield [__DIR__ . '/Fixture/ClassExtendingOld.php', [[$errorMessage, 9]]];
        yield [__DIR__ . '/Fixture/ClassMethodParameterUsingOld.php', [[$errorMessage, 11]]];
        yield [__DIR__ . '/Fixture/SomeStaticCall.php', [[$errorMessage, 13]]];

        $errorMessage = sprintf(
            PreferredClassRule::ERROR_MESSAGE,
            AbstractNotWhatYouWant::class,
            SkipPreferredExtendingTheOldOne::class
        );
        yield [__DIR__ . '/Fixture/InstanceOfName.php', [[$errorMessage, 13]]];

        yield [__DIR__ . '/Fixture/SkipPreferredExtendingTheOldOne.php', []];
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
        return self::getContainer()->getByType(PreferredClassRule::class);
    }
}
