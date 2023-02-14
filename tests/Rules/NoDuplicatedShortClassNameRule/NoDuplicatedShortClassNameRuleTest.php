<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDuplicatedShortClassNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\NoDuplicatedShortClassNameRule;
use Symplify\PHPStanRules\Tests\Rules\NoDuplicatedShortClassNameRule\Fixture\Nested\SameShortName;

/**
 * @extends RuleTestCase<NoDuplicatedShortClassNameRule>
 */
final class NoDuplicatedShortClassNameRuleTest extends RuleTestCase
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

    /**
     * @return Iterator<int, mixed[]>
     */
    public static function provideData(): Iterator
    {
        // might be same, but skipped for shallow nesting - see config file
        yield [
            [
                __DIR__ . '/Fixture/SkipAlreadyExistingShortName.php',
                __DIR__ . '/Source/SkipAlreadyExistingShortName.php',
            ],
            [],
        ];

        $errorMessage = sprintf(
            NoDuplicatedShortClassNameRule::ERROR_MESSAGE,
            'SameShortName',
            implode(
                '", "',
                [SameShortName::class,
                    \Symplify\PHPStanRules\Tests\Rules\NoDuplicatedShortClassNameRule\Fixture\Nested\OneMoreNested\SameShortName::class,
                ]
            )
        );

        yield [
            [
                __DIR__ . '/Fixture/Nested/SameShortName.php',
                __DIR__ . '/Fixture/Nested/OneMoreNested/SameShortName.php',
            ],
            [[$errorMessage, 7]],
        ];
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
        return self::getContainer()->getByType(NoDuplicatedShortClassNameRule::class);
    }
}
