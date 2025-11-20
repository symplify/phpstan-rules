<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\RectorInterface;
use Symplify\PHPStanRules\Enum\RuleIdentifier\RectorRuleIdentifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\NoPropertyNodeAssignRule\NoPropertyNodeAssignRuleTest
 *
 * @implements Rule<Assign>
 */
final class NoPropertyNodeAssignRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid assigning a node to property to avoid object juggling, pass it as argument instead';

    public function getNodeType(): string
    {
        return Assign::class;
    }

    /**
     * @param Assign $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $scope->isInClass()) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection->isSubclassOf(RectorInterface::class)) {
            return [];
        }

        if (! $node->var instanceof PropertyFetch) {
            return [];
        }

        $propertyFetch = $node->var;
        if (! $propertyFetch->var instanceof Variable) {
            return [];
        }

        if (! is_string($propertyFetch->var->name)) {
            return [];
        }

        if ($propertyFetch->var->name !== 'this') {
            return [];
        }

        $assignedExprType = $scope->getType($node->expr);
        if (! $assignedExprType instanceof ObjectType) {
            return [];
        }

        if (! $assignedExprType->isInstanceOf(Node::class)->yes()) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RectorRuleIdentifier::NO_PROPERTY_NODE_ASSIGN)
            ->build();

        return [$identifierRuleError];
    }
}
