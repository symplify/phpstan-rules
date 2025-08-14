<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<Foreach_>
 */
final class ForeachCeptionRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'There is %d nested foreach nested in each other. Refactor to more flat approach or to collection to avoid high complexity';

    private const MAX_NESTED_FOREACHES = 3;

    public function getNodeType(): string
    {
        return Foreach_::class;
    }

    /**
     * @param Foreach_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $nodeFinder = new NodeFinder();

        $nestedForeaches = $nodeFinder->findInstanceOf($node->stmts, Foreach_::class);
        if (count($nestedForeaches) <= self::MAX_NESTED_FOREACHES) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, count($nestedForeaches) + 1))
            ->identifier(RuleIdentifier::RULE_IDENTIFIER)
            ->build();

        return [$identifierRuleError];
    }
}
