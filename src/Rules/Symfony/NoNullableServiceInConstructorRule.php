<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use Closure;
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
use ReflectionProperty;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\NodeAnalyzer\LaravelPresenceResolver;
use Throwable;

/**
 * A constructor service dependency must not be nullable.
 *
 * A service is always provided by the container, so "?SomeService $service" or "SomeService|null $service" only hides
 * that it is really required. Nullable is allowed on an abstract class, whose optional dependency is filled by a child.
 * A nullable scalar, array, exception ("$previous" is nullable by PHP convention), date value object, enum or closure
 * is left alone, as those are values, not services. An exception class is skipped whole - its constructor carries
 * error context, not services - and so is a class with public properties, which holds data. Data-holder classes in
 * an Entity, Event, DTO, Dto, Message, DAO, Dao, Token, Exception, Helper, ValueObject, Form\Type or Badge namespace
 * are skipped whole - their constructors carry values, not services.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\NoNullableServiceInConstructorRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class NoNullableServiceInConstructorRule implements Rule
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;

    /**
     * @readonly
     */
    private LaravelPresenceResolver $laravelPresenceResolver;

    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constructor service "%s" of type "%s" is nullable. A service is always provided, make it non-nullable';

    /**
     * Data-holder namespaces whose constructors carry nullable values, not container services.
     *
     * @var string[]
     */
    private const SKIPPED_NAMESPACE_PARTS = [
        '\\Entity\\',
        '\\Event\\',
        '\\DTO\\',
        '\\Dto\\',
        '\\Message\\',
        '\\DAO\\',
        '\\Dao\\',
        '\\Token\\',
        '\\Exception\\',
        '\\Helper\\',
        '\\ValueObject\\',
        '\\Form\\Type\\',
        '\\Badge\\',
    ];

    public function __construct(ReflectionProvider $reflectionProvider, LaravelPresenceResolver $laravelPresenceResolver)
    {
        $this->reflectionProvider = $reflectionProvider;
        $this->laravelPresenceResolver = $laravelPresenceResolver;
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
        if ($this->laravelPresenceResolver->isLaravelProject()) {
            return [];
        }

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

        // a data-holder namespace (event, DTO, token, exception, helper, form type, badge, ...) carries values, not services
        if ($this->isSkippedNamespace($classReflection->getName())) {
            return [];
        }

        // an exception is built with "new" at the throw site and carries error context, not services
        if ($classReflection->is(Throwable::class)) {
            return [];
        }

        // a class with public properties is a data holder - a service does not expose its dependencies
        if ($this->hasPublicProperty($classReflection)) {
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
     * @param Identifier|Name|ComplexType|null $node
     */
    private function matchClassName(?Node $node): ?Name
    {
        if ($node instanceof Name) {
            return $node;
        }

        return $this->matchNullableServiceName($node);
    }

    /**
     * Returns the class-type name node when the type is a nullable class type, null otherwise.
     * @param Identifier|Name|ComplexType|null $node
     */
    private function matchNullableServiceName(?Node $node): ?Name
    {
        if ($node instanceof NullableType) {
            return $node->type instanceof Name ? $node->type : null;
        }

        if ($node instanceof UnionType) {
            $className = null;
            $hasNull = false;

            foreach ($node->types as $unionedType) {
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
     * Own, promoted and inherited public instance properties count.
     */
    private function hasPublicProperty(ClassReflection $classReflection): bool
    {
        foreach ($classReflection->getNativeReflection()->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            if (! $reflectionProperty->isStatic()) {
                return true;
            }
        }

        return false;
    }

    private function isSkippedNamespace(string $className): bool
    {
        $found = false;
        foreach (self::SKIPPED_NAMESPACE_PARTS as $skippedNamespacePart) {
            if (strpos($className, $skippedNamespacePart) !== false) {
                $found = true;
                break;
            }
        }

        return $found;
    }

    /**
     * A nullable class type that is not really a service: an exception ("$previous"), a date value object, an enum or a
     * closure.
     */
    private function isValueObjectType(string $className): bool
    {
        if (! $this->reflectionProvider->hasClass($className)) {
            return false;
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        if ($classReflection->isEnum()) {
            return true;
        }

        return $classReflection->is(Throwable::class)
            || $classReflection->is(DateTimeInterface::class)
            || $classReflection->is(Closure::class);
    }
}
