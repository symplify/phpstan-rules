<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\FormTypeClassNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Form\AbstractType;
use Symplify\PHPStanRules\Rules\Symfony\FormTypeClassNameRule;
use Symplify\PHPStanRules\Tests\Rules\Symfony\FormTypeClassNameRule\Fixture\SomeType;

final class FormTypeClassNameRuleTest extends RuleTestCase
{
    /**
     * @param array<int, array<string|int>> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<array<int, mixed>, mixed>>
     */
    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeFormType.php', []];

        yield [__DIR__ . '/Fixture/SomeType.php', [
            [
                sprintf(FormTypeClassNameRule::ERROR_MESSAGE, AbstractType::class, SomeType::class),
                9,
            ],
        ]];
    }

    protected function getRule(): Rule
    {
        return new FormTypeClassNameRule();
    }
}
