<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use DateTimeInterface;
use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Throwable;

/**
 * A constructor service dependency must not be nullable.
 *
 * A service is always provided by the container, so "?SomeService $service" or "SomeService|null $service" only hides
 * that it is really required. Nullable is allowed on an abstract class, whose optional dependency is filled by a child.
 * A nullable scalar, array, exception ("$previous" is nullable by PHP convention) or date value object is left alone,
 * as those are values, not services.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\NoNullableServiceInConstructorRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final readonly class NoNullableServiceInConstructorRule implements Rule
{
    public const string ERROR_MESSAGE = 'Constructor service "%s" of type "%s" is nullable. A service is always provided, make it non-nullable';

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     *
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->name->toLowerString() !== '__construct') {
            return [];
        }

        // an anonymous class is a local one-off, not a container service
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection || $classReflection->isAnonymous()) {
            return [];
        }

        // an abstract base class may leave a dependency optional for a child to provide
        if ($classReflection->isAbstract()) {
            return [];
        }

        $paramTypes = $this->resolveParamClassTypes($node, $scope);

        $ruleErrors = [];

        foreach ($node->params as $param) {
            $serviceName = $this->matchNullableServiceName($param->type);
            if (! $serviceName instanceof Name) {
                continue;
            }

            $serviceType = $scope->resolveName($serviceName);
            if ($this->isValueObjectType($serviceType)) {
                continue;
            }

            // a sibling param of the same type already provides it non-nullable, so this one is a real optional extra
            if (count(array_keys($paramTypes, $serviceType, true)) > 1) {
                continue;
            }

            $parameterName = $param->var instanceof Variable && is_string($param->var->name)
                ? '$' . $param->var->name
                : '';

            $ruleErrors[] = RuleErrorBuilder::message(
                sprintf(self::ERROR_MESSAGE, $parameterName, $serviceType)
            )
                ->identifier(RuleIdentifier::NO_NULLABLE_SERVICE_IN_CONSTRUCTOR)
                ->line($param->getStartLine())
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * Resolves every constructor param to its class-type name (nullable or not), for duplicate-type detection.
     *
     * @return string[]
     */
    private function resolveParamClassTypes(ClassMethod $classMethod, Scope $scope): array
    {
        $paramTypes = [];

        foreach ($classMethod->params as $param) {
            $className = $this->matchClassName($param->type);
            if ($className instanceof Name) {
                $paramTypes[] = $scope->resolveName($className);
            }
        }

        return $paramTypes;
    }

    /**
     * Returns the class-type name node of any type, nullable or not, null when the type is not a class type.
     */
    private function matchClassName(Identifier|Name|ComplexType|null $type): ?Name
    {
        if ($type instanceof Name) {
            return $type;
        }

        return $this->matchNullableServiceName($type);
    }

    /**
     * Returns the class-type name node when the type is a nullable class type, null otherwise.
     */
    private function matchNullableServiceName(Identifier|Name|ComplexType|null $type): ?Name
    {
        if ($type instanceof NullableType) {
            return $type->type instanceof Name ? $type->type : null;
        }

        if ($type instanceof UnionType) {
            $className = null;
            $hasNull = false;

            foreach ($type->types as $unionedType) {
                if ($unionedType instanceof Identifier && $unionedType->toLowerString() === 'null') {
                    $hasNull = true;

                    continue;
                }

                if ($unionedType instanceof Name) {
                    $className = $unionedType;
                }
            }

            return $hasNull ? $className : null;
        }

        return null;
    }

    /**
     * A nullable class type that is not really a service: an exception ("$previous") or a date value object.
     */
    private function isValueObjectType(string $className): bool
    {
        if (! $this->reflectionProvider->hasClass($className)) {
            return false;
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        return $classReflection->is(Throwable::class) || $classReflection->is(DateTimeInterface::class);
    }
}
