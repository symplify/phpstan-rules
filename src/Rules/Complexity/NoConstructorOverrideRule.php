<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<ClassMethod>
 */
final class NoConstructorOverrideRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Possible __construct() override, this can cause missing dependencies or setup';

    /**
     * @var string
     */
    private const CONSTRUCTOR_NAME = '__construct';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! fast_node_named($node->name, self::CONSTRUCTOR_NAME)) {
            return [];
        }

        if ($node->stmts === null) {
            return [];
        }

        // has parent constructor call?
        if (! $scope->isInClass()) {
            return [];
        }

        if (! fast_has_parent_constructor($scope)) {
            return [];
        }

        $nodeFinder = new NodeFinder();
        $parentConstructorStaticCall = $nodeFinder->findFirst($node->stmts, function (Node $node): bool {
            if (! $node instanceof StaticCall) {
                return false;
            }

            return fast_node_named($node->name, self::CONSTRUCTOR_NAME);
        });

        if ($parentConstructorStaticCall instanceof StaticCall) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::NO_CONSTRUCTOR_OVERRIDE)
            ->build();

        return [$identifierRuleError];
    }
}
