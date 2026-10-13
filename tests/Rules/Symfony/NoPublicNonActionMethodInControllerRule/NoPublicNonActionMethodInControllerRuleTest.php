<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoPublicNonActionMethodInControllerRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\NoPublicNonActionMethodInControllerRule;

/**
 * @extends RuleTestCase<NoPublicNonActionMethodInControllerRule>
 */
final class NoPublicNonActionMethodInControllerRuleTest extends RuleTestCase
{
    /**
     * @param list<array{0: string, 1: int}> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        $errorMessage = sprintf(NoPublicNonActionMethodInControllerRule::ERROR_MESSAGE, 'helper');

        yield [__DIR__ . '/Fixture/SomeController.php', [[$errorMessage, 9]]];
        yield [__DIR__ . '/Fixture/SkipActionController.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoPublicNonActionMethodInControllerRule();
    }
}
