<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\SeeAnnotationToTestRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\SeeAnnotationToTestRule;
use Symplify\PHPStanRules\Tests\Rules\SeeAnnotationToTestRule\Fixture\RuleWithoutSee;
use Symplify\PHPStanRules\Tests\Rules\SeeAnnotationToTestRule\Fixture\RuleWithSeeRandom;

final class SeeAnnotationToTestRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(SeeAnnotationToTestRule::ERROR_MESSAGE, RuleWithoutSee::class);
        yield [__DIR__ . '/Fixture/RuleWithoutSee.php', [[$errorMessage, 12]]];

        $errorMessage = sprintf(SeeAnnotationToTestRule::ERROR_MESSAGE, RuleWithSeeRandom::class);
        yield [__DIR__ . '/Fixture/RuleWithSeeRandom.php', [[$errorMessage, 15]]];

        yield [__DIR__ . '/Fixture/SkipDeprecatedRuleWithoutSee.php', []];
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
        return self::getContainer()->getByType(SeeAnnotationToTestRule::class);
    }
}
