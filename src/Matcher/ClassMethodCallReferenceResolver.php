<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Matcher;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Node\MethodCallableNode;
use PHPStan\Type\ThisType;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeWithClassName;
use Symplify\PHPStanRules\ValueObject\MethodCallReference;

final class ClassMethodCallReferenceResolver
{
    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PHPStan\Node\MethodCallableNode $methodCallOrMethodCallable
     */
    public function resolve($methodCallOrMethodCallable, Scope $scope, bool $allowThisType): ?MethodCallReference
    {
        if ($methodCallOrMethodCallable instanceof MethodCallableNode) {
            $methodName = $methodCallOrMethodCallable->getName();
            $variable = $methodCallOrMethodCallable->getVar();
        } else {
            $methodName = $methodCallOrMethodCallable->name;
            $variable = $methodCallOrMethodCallable->var;
        }

        if ($methodName instanceof Expr) {
            return null;
        }

        $callerType = $scope->getType($variable);

        // remove optional nullable type
        if (TypeCombinator::containsNull($callerType)) {
            $callerType = TypeCombinator::removeNull($callerType);
        }

        if (! $allowThisType && $callerType instanceof ThisType) {
            return null;
        }

        if (! $callerType instanceof TypeWithClassName) {
            return null;
        }

        // move to the class where method is defined, e.g. parent class defines the method, so it should be checked there
        $className = $callerType->getClassName();
        $methodNameString = $methodName->toString();

        return new MethodCallReference($className, $methodNameString);
    }
}
