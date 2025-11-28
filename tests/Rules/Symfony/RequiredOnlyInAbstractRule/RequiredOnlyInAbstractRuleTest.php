<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequiredOnlyInAbstractRule;

use Iterator;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\Rules\Symfony\RequiredOnlyInAbstractRule;

final class RequiredOnlyInAbstractRuleTest extends RuleTestCase
{
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/NonAbstractControllerWithRequired.php', [[
            RequiredOnlyInAbstractRule::ERROR_MESSAGE,
            12,
        ]]];

        yield [__DIR__ . '/Fixture/SkipCircularNote.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractClass.php', []];
        yield [__DIR__ . '/Fixture/SkipParentDocumentRepository.php', []];
    }

    protected function getRule(): RequiredOnlyInAbstractRule
    {
        return new RequiredOnlyInAbstractRule();
    }
}
