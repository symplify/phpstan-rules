<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenExtendOfNonAbstractClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\ForbiddenExtendOfNonAbstractClassRule;

final class ForbiddenExtendOfNonAbstractClassRuleTest extends RuleTestCase
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
        yield [
            __DIR__ . '/Fixture/ClassExtendingNonAbstractClass.php',
            [[ForbiddenExtendOfNonAbstractClassRule::ERROR_MESSAGE, 9]], ];

        yield [__DIR__ . '/Fixture/SkipVendorBasedClasses.php', []];
        yield [__DIR__ . '/Fixture/SkipClassExtendingAbstractClass.php', []];
        yield [__DIR__ . '/Fixture/SkipException.php', []];
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
        return self::getContainer()->getByType(ForbiddenExtendOfNonAbstractClassRule::class);
    }
}
