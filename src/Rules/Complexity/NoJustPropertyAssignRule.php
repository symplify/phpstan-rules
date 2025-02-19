<?php

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\Form\AbstractType;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\PhpDoc\PhpDocResolver;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\NoJustPropertyAssignRule\NoJustPropertyAssignRuleTest
 *
 * @implements Rule<Expression>
 */
final class NoJustPropertyAssignRule implements Rule
{
    /**
     * @readonly
     */
    private PhpDocResolver $phpDocResolver;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of assigning service property to a variable, use the property directly';

    public function __construct(PhpDocResolver $phpDocResolver)
    {
        $this->phpDocResolver = $phpDocResolver;
    }

    public function getNodeType(): string
    {
        return Expression::class;
    }

    /**
     * @param Expression $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $expr = $node->expr;

        if (! $expr instanceof Assign) {
            return [];
        }

        if (! $this->isLocalPropertyFetchAssignToVariable($expr, $scope)) {
            return [];
        }

        if ($this->shouldSkipCurrentClass($scope)) {
            return [];
        }

        if ($this->shoulSkipMoreSpecificTypeByDocblock($scope, $node, $expr)) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::NO_JUST_PROPERTY_ASSIGN)
            ->build()];
    }

    private function shoulSkipMoreSpecificTypeByDocblock(Scope $scope, Expression $expression, Assign $assign): bool
    {
        $docComment = $expression->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        /** @var Variable $variable */
        $variable = $assign->var;
        $varName = $variable->name;

        if ($varName instanceof Expr) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        $resolvedPhpDocBlock = $this->phpDocResolver->resolve($scope, $classReflection, $docComment);
        $exprType = $scope->getType($assign->expr);

        foreach ($resolvedPhpDocBlock->getVarTags() as $key => $varTag) {
            if ($key !== $varName) {
                continue;
            }

            // different type means more specific type on purpose
            if (! $varTag->getType()->equals($exprType)) {
                return true;
            }
        }

        return false;
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
