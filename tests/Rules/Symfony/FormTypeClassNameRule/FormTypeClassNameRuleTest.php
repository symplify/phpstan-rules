<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\FormTypeClassNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\FormTypeClassNameRule;

final class FormTypeClassNameRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeFormType.php', []];

        yield [__DIR__ . '/Fixture/SomeType.php', [
            [
                'Class extends "Symfony\Component\Form\AbstractType" must have "FormType" suffix to make form explicit, "Symplify\PHPStanRules\Tests\Rules\Symfony\FormTypeClassNameRule\Fixture\SomeType" given',
                9,
            ],
        ]];
    }

    protected function getRule(): Rule
    {
        return new FormTypeClassNameRule();
    }
}
