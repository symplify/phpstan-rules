<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Contracts\Service\Attribute\Required;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * A service injected in __construct() or an autowire*() method must not be handed over to another method call.
 *
 * Passing an own dependency around is service juggling - the service travels through a parameter list instead of
 * being injected where it is used. If the called method belongs to the same class, it has a constructor of its own
 * and can take the service directly.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\NoServiceJugglingRuleTest
 *
 * @implements Rule<InClassNode>
 */
final class NoServiceJugglingRule implements Rule
{
    public const string ERROR_MESSAGE = 'Service "$this->%s" is passed to "%s()" as an argument. Inject "%s" in the constructor of the class that uses it instead';

    private const string CONSTRUCTOR_NAME = '__construct';

    private const string AUTOWIRE_PREFIX = 'autowire';

    private const string REQUIRED_ATTRIBUTE = Required::class;

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     *
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        $classReflection = $node->getClassReflection();

        $injectedServiceTypes = $this->resolveInjectedServiceTypes($classLike);
        if ($injectedServiceTypes === []) {
            return [];
        }

        $ownMethodCalls = $this->resolveOwnMethodCalls($classLike, $classReflection);
        if ($ownMethodCalls === []) {
            return [];
        }

        $constantArgumentKeys = $this->resolveConstantArgumentKeys($ownMethodCalls);

        $ruleErrors = [];

        foreach ($ownMethodCalls as [$call, $calledMethodName]) {
            foreach ($call->getArgs() as $position => $arg) {
                $propertyName = $this->matchThisPropertyName($arg->value);
                if ($propertyName === null || ! isset($injectedServiceTypes[$propertyName])) {
                    continue;
                }

                if (! isset($constantArgumentKeys[$this->createArgumentKey($call, $calledMethodName, $arg, $position)])) {
                    continue;
                }

                $ruleErrors[] = RuleErrorBuilder::message(sprintf(
                    self::ERROR_MESSAGE,
                    $propertyName,
                    $calledMethodName,
                    $injectedServiceTypes[$propertyName]
                ))
                    ->identifier(RuleIdentifier::NO_SERVICE_JUGGLING)
                    ->line($arg->getStartLine())
                    ->build();
            }
        }

        return $ruleErrors;
    }

    /**
     * Properties filled by __construct() or an autowire*() method, e.g. "userHelper" => "App\UserHelper".
     *
     * @return array<string, string>
     */
    private function resolveInjectedServiceTypes(Class_ $class): array
    {
        $injectedServiceTypes = [];

        foreach ($class->getMethods() as $classMethod) {
            if (! $this->isInjectingMethod($classMethod)) {
                continue;
            }

            $paramTypes = [];

            foreach ($classMethod->params as $param) {
                $className = $this->matchObjectTypeName($param);
                if ($className === null) {
                    continue;
                }

                if (! $param->var instanceof Variable || ! is_string($param->var->name)) {
                    continue;
                }

                $paramTypes[$param->var->name] = $className;

                // promoted property keeps the param name
                if ($param->flags !== 0) {
                    $injectedServiceTypes[$param->var->name] = $className;
                }
            }

            foreach ($this->resolveAssignedProperties($classMethod) as $propertyName => $variableName) {
                if (isset($paramTypes[$variableName])) {
                    $injectedServiceTypes[$propertyName] = $paramTypes[$variableName];
                }
            }
        }

        return $injectedServiceTypes;
    }

    private function isInjectingMethod(ClassMethod $classMethod): bool
    {
        $methodName = $classMethod->name->toString();

        if ($classMethod->name->toLowerString() === self::CONSTRUCTOR_NAME) {
            return true;
        }

        if (str_starts_with($methodName, self::AUTOWIRE_PREFIX)) {
            return true;
        }

        foreach ($classMethod->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === self::REQUIRED_ATTRIBUTE) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * The "$this->userHelper = $userHelper;" assignments, as "userHelper" => "userHelper".
     *
     * @return array<string, string>
     */
    private function resolveAssignedProperties(ClassMethod $classMethod): array
    {
        $assignedProperties = [];

        $nodeFinder = new NodeFinder();

        /** @var Assign[] $assigns */
        $assigns = $nodeFinder->findInstanceOf((array) $classMethod->stmts, Assign::class);

        foreach ($assigns as $assign) {
            $propertyName = $this->matchThisPropertyName($assign->var);
            if ($propertyName === null) {
                continue;
            }

            if (! $assign->expr instanceof Variable || ! is_string($assign->expr->name)) {
                continue;
            }

            $assignedProperties[$propertyName] = $assign->expr->name;
        }

        return $assignedProperties;
    }

    /**
     * The calls that stay inside the class hierarchy: "$this->someMethod()" and "parent::someMethod()".
     *
     * @return list<MethodCall|StaticCall>
     */
    private function resolveLocalCalls(ClassMethod $classMethod): array
    {
        $nodeFinder = new NodeFinder();

        /** @var array<MethodCall|StaticCall> $calls */
        $calls = $nodeFinder->find((array) $classMethod->stmts, static function (Node $node): bool {
            if ($node instanceof MethodCall) {
                return $node->var instanceof Variable && $node->var->name === 'this';
            }

            if ($node instanceof StaticCall) {
                return $node->class instanceof Name && $node->class->toLowerString() === 'parent';
            }

            return false;
        });

        return array_values($calls);
    }

    /**
     * The calls to a method the class itself owns, so the dependency can be moved into a constructor.
     *
     * @return list<array{MethodCall|StaticCall, string}>
     */
    private function resolveOwnMethodCalls(Class_ $class, ClassReflection $classReflection): array
    {
        $ownMethodCalls = [];

        foreach ($class->getMethods() as $classMethod) {
            foreach ($this->resolveLocalCalls($classMethod) as $call) {
                // "$this->toText(...)" has no arguments to look at
                if ($call->isFirstClassCallable()) {
                    continue;
                }

                $calledMethodName = $call->name instanceof Identifier ? $call->name->toString() : null;
                if ($calledMethodName === null) {
                    continue;
                }

                if (! $this->isOwnMethod($call, $class, $classReflection, $calledMethodName)) {
                    continue;
                }

                $ownMethodCalls[] = [$call, $calledMethodName];
            }
        }

        return $ownMethodCalls;
    }

    /**
     * A method the class can change: one declared right here, or the one of the parent an explicit "parent::" targets.
     *
     * An inherited method reached through "$this->" is left out: it is shared by every child, so the service is an
     * argument of the call, not a dependency of the method. The same goes for a method brought in by a trait, as a
     * trait has no constructor.
     *
     * @param MethodCall|StaticCall $call
     */
    private function isOwnMethod(Node $call, Class_ $class, ClassReflection $classReflection, string $methodName): bool
    {
        if ($call instanceof StaticCall) {
            return ! $this->isDeclaredInTrait($classReflection->getParentClass(), $methodName);
        }

        return $class->getMethod($methodName) instanceof ClassMethod;
    }

    /**
     * The argument positions that always get the very same value, as only those stand for a fixed dependency.
     * A position filled differently by another call is a parameter of its own.
     *
     * @param list<array{MethodCall|StaticCall, string}> $ownMethodCalls
     *
     * @return array<string, true>
     */
    private function resolveConstantArgumentKeys(array $ownMethodCalls): array
    {
        $argumentValues = [];

        foreach ($ownMethodCalls as [$call, $calledMethodName]) {
            foreach ($call->getArgs() as $position => $arg) {
                $argumentKey = $this->createArgumentKey($call, $calledMethodName, $arg, $position);

                $argumentValues[$argumentKey][$this->matchThisPropertyName($arg->value) ?? '#other'] = true;
            }
        }

        $constantArgumentKeys = [];

        foreach ($argumentValues as $argumentKey => $values) {
            if (count($values) === 1) {
                $constantArgumentKeys[$argumentKey] = true;
            }
        }

        return $constantArgumentKeys;
    }

    /**
     * @param MethodCall|StaticCall $call
     */
    private function createArgumentKey(Node $call, string $calledMethodName, Arg $arg, int $position): string
    {
        $callKind = $call instanceof StaticCall ? 'parent' : 'this';
        $argName = $arg->name instanceof Identifier ? $arg->name->toString() : (string) $position;

        return $callKind . '::' . $calledMethodName . '#' . $argName;
    }

    /**
     * A trait has no constructor to inject into, so its methods can only take the service as a parameter.
     */
    private function isDeclaredInTrait(?ClassReflection $classReflection, string $methodName): bool
    {
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return array_any(
            $classReflection->getTraits(true),
            fn (ClassReflection $classReflection): bool => $classReflection->hasNativeMethod($methodName)
        );
    }

    /**
     * The property name of "$this->userHelper", null for anything else.
     */
    private function matchThisPropertyName(Node $node): ?string
    {
        if (! $node instanceof PropertyFetch) {
            return null;
        }

        if (! $node->var instanceof Variable || $node->var->name !== 'this') {
            return null;
        }

        if (! $node->name instanceof Identifier) {
            return null;
        }

        return $node->name->toString();
    }

    /**
     * Only class types count, a scalar or an array is a value, not a service.
     */
    private function matchObjectTypeName(Param $param): ?string
    {
        $type = $param->type;
        if ($type instanceof NullableType) {
            $type = $type->type;
        }

        if (! $type instanceof Name) {
            return null;
        }

        return $type->toString();
    }
}
