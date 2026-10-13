<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\TypeCombinator;

/**
 * A getter of a nullable property must not declare a non-nullable return type.
 *
 * When a property is typed "?Foo" it can hold null, so a "getFoo(): Foo" that returns it lies about the type.
 * Either the property is never null (drop the "?") or the getter is honest ("getFoo(): ?Foo").
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\NullablePropertyGetterMustReturnNullableRule\NullablePropertyGetterMustReturnNullableRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class NullablePropertyGetterMustReturnNullableRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Getter "%s()" returns nullable property "$%s" but declares a non-nullable return type. Make the return type nullable';

    /**
     * @var string
     */
    private const GET_PREFIX = 'get';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->isPublic()) {
            return [];
        }

        if (strncmp($node->name->toLowerString(), self::GET_PREFIX, strlen(self::GET_PREFIX)) !== 0) {
            return [];
        }

        if ($node->returnType === null || $this->returnTypeAcceptsNull($node->returnType)) {
            return [];
        }

        $propertyName = $this->matchReturnedPropertyName($node);
        if ($propertyName === null) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection || ! $classReflection->hasNativeProperty($propertyName)) {
            return [];
        }

        $propertyType = $classReflection->getNativeProperty($propertyName)
            ->getNativeType();
        if (! TypeCombinator::containsNull($propertyType)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $node->name->toString(), $propertyName);

        return [
            RuleErrorBuilder::message($errorMessage)
                ->identifier('symplify.nullablePropertyGetterMustReturnNullable')
                ->build(),
        ];
    }

    private function returnTypeAcceptsNull(Node $returnType): bool
    {
        if ($returnType instanceof NullableType) {
            return true;
        }

        if ($returnType instanceof UnionType) {
            foreach ($returnType->types as $type) {
                if ($type instanceof Identifier && $type->toLowerString() === 'null') {
                    return true;
                }

                if ($type instanceof Name && $type->toLowerString() === 'null') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the property name when the method body is exactly "return $this->property;", null otherwise.
     */
    private function matchReturnedPropertyName(ClassMethod $classMethod): ?string
    {
        if ($classMethod->stmts === null || count($classMethod->stmts) !== 1) {
            return null;
        }

        $onlyStmt = $classMethod->stmts[0];
        if (! $onlyStmt instanceof Return_) {
            return null;
        }

        $returnedExpr = $onlyStmt->expr;
        if (! $returnedExpr instanceof PropertyFetch) {
            return null;
        }

        if (! $returnedExpr->var instanceof Variable || $returnedExpr->var->name !== 'this') {
            return null;
        }

        if (! $returnedExpr->name instanceof Identifier) {
            return null;
        }

        return $returnedExpr->name->toString();
    }
}
