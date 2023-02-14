<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredAttributeOverAnnotationRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PHPStanRules\Rules\PreferredAttributeOverAnnotationRule;

final class PreferredAttributeOverAnnotationRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAttributeRoute.php', []];

        $errorMessage = sprintf(PreferredAttributeOverAnnotationRule::ERROR_MESSAGE, Route::class);

        yield [__DIR__ . '/Fixture/SomeAnnotatedController.php', [[$errorMessage, 14]]];
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
        return self::getContainer()->getByType(PreferredAttributeOverAnnotationRule::class);
    }
}
