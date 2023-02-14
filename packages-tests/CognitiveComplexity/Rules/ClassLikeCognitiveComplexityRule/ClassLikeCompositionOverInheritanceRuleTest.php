<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule;

final class ClassLikeCompositionOverInheritanceRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideDataForTest')]
    public function test(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideDataForTest(): Iterator
    {
        $errorMessage = sprintf(ClassLikeCognitiveComplexityRule::ERROR_MESSAGE, 54, 50);
        yield [__DIR__ . '/Fixture/ClassWithManyComplexMethods.php', [[$errorMessage, 7]]];

        $errorMessage = sprintf(ClassLikeCognitiveComplexityRule::ERROR_MESSAGE, 34, 5);
        yield [__DIR__ . '/Fixture/SimpleCommand.php', [[$errorMessage, 9]]];

        $errorMessage = sprintf(ClassLikeCognitiveComplexityRule::ERROR_MESSAGE, 44, 5);
        yield [__DIR__ . '/Fixture/NonFinalClass.php', [[$errorMessage, 9]]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_composition_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(ClassLikeCognitiveComplexityRule::class);
    }
}
