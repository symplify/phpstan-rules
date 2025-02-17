<?php

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\Form\AbstractType;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\NoJustPropertyAssignRule\NoJustPropertyAssignRuleTest
 *
 * @implements Rule<Assign>
 */
final class NoJustPropertyAssignRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of assigning service property to a variable, use the property directly';

    public function getNodeType(): string
    {
        return Assign::class;
    }

    /**
     * @param Assign $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->isLocalPropertyFetchAssignToVariable($node, $scope)) {
            return [];
        }

        if ($this->shouldSkipCurrentClass($scope)) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::NO_JUST_PROPERTY_ASSIGN)
            ->build()];
    }

    private function isLocalPropertyFetchAssignToVariable(Assign $assign, Scope $scope): bool
    {
        if (! $assign->var instanceof Variable) {
            return false;
        }

        if (! $assign->expr instanceof PropertyFetch) {
            return false;
        }

        $propertyFetch = $assign->expr;
        if (! $propertyFetch->var instanceof Variable) {
            return false;
        }

        if ($propertyFetch->var->name !== 'this') {
            return false;
        }

        $exprType = $scope->getType($assign->expr);
        return $exprType->isObject()->yes();
    }

    private function shouldSkipCurrentClass(Scope $scope): bool
    {
        // skip entities as rather static
        if (strpos($scope->getFile(), '/Document/') !== false || strpos($scope->getFile(), '/Entity/') !== false) {
            return true;
        }

        if ($scope->isInClass()) {
            $classReflection = $scope->getClassReflection();

            // skip Symfony form types as rather static
            if ($classReflection->isSubclassOf(AbstractType::class)) {
                return true;
            }
        }

        return false;
    }
}
