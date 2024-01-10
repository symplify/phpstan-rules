<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\ReturnTypeExtension;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

final class GetAttributeReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    /**
     * @var array<string, string>
     */
    private const ARGUMENT_KEY_TO_RETURN_TYPE = [
        'Rector\NodeTypeResolver\Node\AttributeKey::SCOPE' => Scope::class,
        # Node
        'Rector\NodeTypeResolver\Node\AttributeKey::ORIGINAL_NODE' => Node::class,
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

        $argumentValue = $this->resolveArgumentValue($methodCall->getArgs()[0]->value);
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
        if ($expr instanceof ClassConstFetch) {
            if (! $expr->class instanceof Name) {
                return null;
            }

            $className = $expr->class->toString();

            if (! $expr->name instanceof Identifier) {
                return null;
            }

            $constName = $expr->name->toString();

            return $className . '::' . $constName;
        }
        return null;
    }
}
