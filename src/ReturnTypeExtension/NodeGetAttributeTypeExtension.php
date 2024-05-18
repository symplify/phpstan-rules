<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ReturnTypeExtension;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Rector\NodeTypeResolver\Node\AttributeKey;

/**
 * @api used in rector rules
 * @see \Symplify\PHPStanRules\Tests\ReturnTypeExtension\NodeGetAttributeTypeExtension\NodeGetAttributeTypeExtensionTest
 */
final class NodeGetAttributeTypeExtension implements DynamicMethodReturnTypeExtension
{
    /**
     * @var array<string, string>
     */
    private const ARGUMENT_KEY_TO_RETURN_TYPE = [
        'scope' => Scope::class,
        AttributeKey::class . '::SCOPE' => Scope::class,
        'originalNode' => Node::class,
        AttributeKey::class . '::ORIGINAL_NODE' => Node::class,
    ];

    public function getClass(): string
    {
        return Node::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'getAttribute';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();

        $firstArg = $methodCall->getArgs()[0];

        $argumentValue = $this->resolveArgumentValue($firstArg->value);
        if ($argumentValue === null) {
            return $returnType;
        }

        if (! isset(self::ARGUMENT_KEY_TO_RETURN_TYPE[$argumentValue])) {
            return $returnType;
        }

        $knownReturnType = self::ARGUMENT_KEY_TO_RETURN_TYPE[$argumentValue];
        return new UnionType([new ObjectType($knownReturnType), new NullType()]);
    }

    private function resolveArgumentValue(Expr $expr): ?string
    {
        if ($expr instanceof String_) {
            return $expr->value;
        }

        if ($expr instanceof ClassConstFetch) {
            if (! $expr->class instanceof FullyQualified) {
                return null;
            }

            if (! $expr->name instanceof Identifier) {
                return null;
            }

            $className = $expr->class->toString();
            $value = $expr->name->toString();

            return $className . '::' . $value;
        }

        return null;
    }
}
