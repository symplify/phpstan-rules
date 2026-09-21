<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * An object property must not be assigned from another object property of the same object.
 *
 * "$this->repository = $this->someRepository;" keeps the very same service under 2 names, so both properties have to be
 * kept in sync forever. Use the original property directly instead and drop the duplicate one.
 *
 * Only object properties are reported - a scalar or array property is often a deliberate snapshot of a previous state,
 * e.g. "$this->bodyInitial = $this->body;". Anonymous classes are skipped, as they are local one-offs.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertyToPropertyAssignRule\NoPropertyToPropertyAssignRuleTest
 *
 * @implements Rule<Assign>
 */
final class NoPropertyToPropertyAssignRule implements Rule
{
    public const string ERROR_MESSAGE = 'Property "$this->%s" must not be assigned from property "$this->%s". Use the original property directly instead';

    public function getNodeType(): string
    {
        return Assign::class;
    }

    /**
     * @param Assign $node
     *
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // an anonymous class is a local one-off, e.g. a test double filling a parent property
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection || $classReflection->isAnonymous()) {
            return [];
        }

        $assignedPropertyName = $this->matchThisPropertyName($node->var);
        if ($assignedPropertyName === null) {
            return [];
        }

        $sourcePropertyName = $this->matchThisPropertyName($node->expr);
        if ($sourcePropertyName === null) {
            return [];
        }

        // "$this->items = $this->items" is a different smell, not a duplicated property
        if ($assignedPropertyName === $sourcePropertyName) {
            return [];
        }

        // a scalar or array property is often a deliberate snapshot of a previous state
        if ($scope->getType($node->expr)->getObjectClassNames() === []) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(
            sprintf(self::ERROR_MESSAGE, $assignedPropertyName, $sourcePropertyName)
        )
            ->identifier(RuleIdentifier::NO_PROPERTY_TO_PROPERTY_ASSIGN)
            ->build();

        return [$identifierRuleError];
    }

    /**
     * Returns the property name of a "$this->someProperty" fetch, null for anything else.
     */
    private function matchThisPropertyName(Expr $expr): ?string
    {
        if (! $expr instanceof PropertyFetch) {
            return null;
        }

        if (! $expr->var instanceof Variable || $expr->var->name !== 'this') {
            return null;
        }

        if (! $expr->name instanceof Identifier) {
            return null;
        }

        return $expr->name->toString();
    }
}
