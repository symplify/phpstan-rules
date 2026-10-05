<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<Assign>
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingVariableDimFetchRule\NoMissingVariableDimFetchRuleTest
 */
final class NoMissingVariableDimFetchRule implements Rule
{
    /**
     * @api
     * @var string
     */
    public const ERROR_MESSAGE = 'Dim fetch assign variable is missing, create it first';

    public function getNodeType(): string
    {
        return Assign::class;
    }

    /**
     * @param Assign $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->var instanceof ArrayDimFetch) {
            return [];
        }

        $arrayDimFetch = $node->var;
        if (! $arrayDimFetch->var instanceof Variable) {
            return [];
        }

        $dimFetchVariable = $arrayDimFetch->var;

        if (! is_string($dimFetchVariable->name)) {
            return [];
        }

        if (! $scope->hasVariableType($dimFetchVariable->name)->no()) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::NO_MISSING_VARIABLE_DIM_FETCH)
            ->build();

        return [$identifierRuleError];
    }
}
