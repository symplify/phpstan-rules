<?php

namespace Symplify\PHPStanRules\Rules\PHPUnit;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\PHPUnitRuleIdentifier;

/**
 * @implements Rule<FuncCall>
 */
final class NoAssertFuncCallInTestsRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of assert() that can miss important checks, use native PHPUnit assert call';

    private const TEST_FILE_SUFFIXES = [
        'Test.php',
        'TestCase.php',
        'Context.php',
    ];

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @param FuncCall $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Name) {
            return [];
        }

        if ($node->name->toString() !== 'assert') {
            return [];
        }

        if (! $this->isTestFile($scope)) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(PHPUnitRuleIdentifier::NO_ASSERT_FUNC_CALL_IN_TESTS)
            ->build();

        return [$identifierRuleError];
    }

    private function isTestFile(Scope $scope): bool
    {
        foreach (self::TEST_FILE_SUFFIXES as $testFileSuffix) {
            if (substr_compare($scope->getFile(), $testFileSuffix, -strlen($testFileSuffix)) === 0) {
                return true;
            }
        }

        return false;
    }
}
