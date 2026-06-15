<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Reflection;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Symplify\PHPStanRules\Helper\NamingHelper;

final class ClassConstructorTypesResolver
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    /**
     * @return array<string, string>
     */
    public function resolveClassConstructorNamesToTypes(MethodCall $methodCall): array
    {
        $serviceClassOrName = $this->resolveClassNameFromServicesSetMethodCall($methodCall);
        if (! is_string($serviceClassOrName)) {
            return [];
        }

        $classArgumentNamesToTypes = [];
        if (! $this->reflectionProvider->hasClass($serviceClassOrName)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($serviceClassOrName);
        if (! $classReflection->hasConstructor()) {
            return [];
        }

        $extendedMethodReflection = $classReflection->getConstructor();

        foreach ($extendedMethodReflection->getOnlyVariant()->getParameters() as $extendedParameterReflection) {
            $parameterType = $extendedParameterReflection->getType();
            if (! $parameterType instanceof ObjectType) {
                continue;
            }

            $classArgumentNamesToTypes[$extendedParameterReflection->getName()] = $parameterType->getClassName();
        }

        return $classArgumentNamesToTypes;
    }

    private function resolveClassNameFromServicesSetMethodCall(MethodCall $methodCall): ?string
    {
        $currentMethodCall = $methodCall;
        while ($currentMethodCall->var instanceof MethodCall) {
            $currentMethodCall = $currentMethodCall->var;

            if (! NamingHelper::isName($currentMethodCall->name, 'set')) {
                continue;
            }

            $serviceClassOrName = $currentMethodCall->getArgs()[0]->value;
            if ($serviceClassOrName instanceof ClassConstFetch) {
                return NamingHelper::getName($serviceClassOrName->class);
            }

            $secondArg = $currentMethodCall->getArgs()[1] ?? null;
            if ($secondArg instanceof Arg) {
                $secondExpr = $secondArg->value;
                if ($secondExpr instanceof ClassConstFetch) {
                    return NamingHelper::getName($secondExpr->class);
                }
            }
        }

        return null;
    }
}
