<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\UnionType;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Flags a method that takes a class-type union param (A|B) but declares a broader single return type
 * (e.g. their shared parent Node), then returns that very param.
 * The return type should be the explicit union the param carries, not the broad parent.
 *
 * @implements Rule<ClassMethod>
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\NarrowReturnToUnionParamRule\NarrowReturnToUnionParamRuleTest
 */
final class NarrowReturnToUnionParamRule implements Rule
{
    public const ERROR_MESSAGE = 'Return type "%s" is too broad for a returned union param. Declare the explicit union return type "%s" instead.';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $returnName = $this->resolveSingleClassReturnName($node->returnType);
        if ($returnName === null) {
            return [];
        }

        foreach ($node->params as $param) {
            $paramNames = $this->resolveClassUnionNames($param->type);
            if ($paramNames === null) {
                continue;
            }

            // return type is one of the union members, not a broad shared parent - already narrow enough
            if (in_array($returnName, $paramNames, true)) {
                continue;
            }

            if (! $param->var instanceof Variable || ! is_string($param->var->name)) {
                continue;
            }

            if (! $this->methodReturnsVariable($node, $param->var->name)) {
                continue;
            }

            $shortNames = array_map(static function (string $name): string {
                $parts = explode('\\', $name);
                return end($parts);
            }, $paramNames);

            if ($this->isNullableReturnType($node->returnType)) {
                $shortNames[] = 'null';
            }

            return [
                RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $returnName, implode('|', $shortNames)))
                    ->identifier('rector.explicitUnionReturnType')
                    ->line($node->getStartLine())
                    ->build(),
            ];
        }

        return [];
    }

    private function methodReturnsVariable(ClassMethod $classMethod, string $variableName): bool
    {
        if ($classMethod->stmts === null) {
            return false;
        }

        $nodeFinder = new NodeFinder();
        $foundReturn = $nodeFinder->findFirst(
            $classMethod->stmts,
            static fn (Node $subNode): bool => $subNode instanceof Return_
                && $subNode->expr instanceof Variable
                && $subNode->expr->name === $variableName
        );

        return $foundReturn instanceof Return_;
    }

    private function isNullableReturnType(?Node $type): bool
    {
        if ($type instanceof NullableType) {
            return true;
        }

        if (! $type instanceof UnionType) {
            return false;
        }

        foreach ($type->types as $memberType) {
            if ($memberType instanceof Identifier && $memberType->toLowerString() === 'null') {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the single class return type name (nullable allowed), or null when the return type is
     * missing, scalar, or a union of 2+ class types - the latter is already an explicit union.
     */
    private function resolveSingleClassReturnName(?Node $type): ?string
    {
        if ($type instanceof NullableType) {
            $type = $type->type;
        }

        if ($type instanceof Name) {
            return $type->toString();
        }

        if (! $type instanceof UnionType) {
            return null;
        }

        $names = [];
        foreach ($type->types as $memberType) {
            if ($memberType instanceof Identifier) {
                if ($memberType->toLowerString() === 'null') {
                    continue;
                }

                return null;
            }

            if (! $memberType instanceof Name) {
                return null;
            }

            $names[] = $memberType->toString();
        }

        if (count($names) !== 1) {
            return null;
        }

        return $names[0];
    }

    /**
     * Returns sorted class-type member names (null stripped), or null when the type is not a
     * union of 2+ class types - scalars, array and intersections disqualify it.
     *
     * @return list<string>|null
     */
    private function resolveClassUnionNames(?Node $type): ?array
    {
        if (! $type instanceof UnionType) {
            return null;
        }

        $names = [];
        foreach ($type->types as $memberType) {
            if ($memberType instanceof Identifier) {
                if ($memberType->toLowerString() === 'null') {
                    continue;
                }

                return null;
            }

            if (! $memberType instanceof Name) {
                return null;
            }

            $names[] = $memberType->toString();
        }

        if (count($names) < 2) {
            return null;
        }

        $names = array_unique($names);
        sort($names);

        return $names;
    }
}
