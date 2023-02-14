<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ExclusiveNamespaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\ExclusiveNamespaceRule;

final class ExclusiveNamespaceRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(
            ExclusiveNamespaceRule::ERROR_MESSAGE,
            'Symplify\PHPStanRules\Tests\Rules\ExclusiveNamespaceRule\Fixture\Presenter',
            'Presenter'
        );
        yield [__DIR__ . '/Fixture/Presenter/SomeRepository.php', [[$errorMessage, 7]]];

        yield [__DIR__ . '/Fixture/Presenter/Contract/SkipContract.php', []];
        yield [__DIR__ . '/Fixture/Presenter/Exception/SkipException.php', []];
        yield [__DIR__ . '/Fixture/Presenter/SkipSomeTest.php', []];
        yield [__DIR__ . '/Fixture/Presenter/SkipPresenter.php', []];
        yield [__DIR__ . '/Fixture/SkipNotMatched.php', []];
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
        return self::getContainer()->getByType(ExclusiveNamespaceRule::class);
    }
}
