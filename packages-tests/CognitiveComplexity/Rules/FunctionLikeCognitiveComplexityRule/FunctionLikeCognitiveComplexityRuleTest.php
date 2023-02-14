<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule;
use Symplify\PHPStanRules\Tests\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule\Fixture\ClassMethodOverComplicated;
use Symplify\PHPStanRules\Tests\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule\Fixture\VideoRepository;

final class FunctionLikeCognitiveComplexityRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(FunctionLikeCognitiveComplexityRule::ERROR_MESSAGE, 'someFunction()', 9, 8);
        yield [__DIR__ . '/Fixture/function.php.inc', [[$errorMessage, 3]]];

        $errorMessage = sprintf(
            FunctionLikeCognitiveComplexityRule::ERROR_MESSAGE,
            ClassMethodOverComplicated::class . '::someMethod()',
            9,
            8
        );
        yield [__DIR__ . '/Fixture/ClassMethodOverComplicated.php', [[$errorMessage, 7]]];

        $errorMessage = sprintf(
            FunctionLikeCognitiveComplexityRule::ERROR_MESSAGE,
            VideoRepository::class . '::findBySlug()',
            9,
            8
        );
        yield [__DIR__ . '/Fixture/VideoRepository.php', [[$errorMessage, 12]]];
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
        return self::getContainer()->getByType(FunctionLikeCognitiveComplexityRule::class);
    }
}
