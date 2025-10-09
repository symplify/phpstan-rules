<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;
use ReflectionAttribute;
use ReflectionMethod;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Enum\SymfonyClass;
use Symplify\PHPStanRules\Reflection\InvokeClassMethodResolver;

/**
 * To pass a controller class in $this->router->generate(SomeController::class),
 * the controller must be present #[Route(name:: self::class)
 *
 * @see https://symfony.com/blog/new-in-symfony-6-4-fqcn-based-routes
 *
 * @implements Rule<MethodCall>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule\RequireRouteNameToGenerateControllerRouteRuleTest
 */
final class RequireRouteNameToGenerateControllerRouteRule implements Rule
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    /**
     * @api
     * @var string
     */
    public const ERROR_MESSAGE = 'To pass a controller class to generate() method, the controller must have "#[Route(name: self::class)]" above the __invoke() method';

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->isRouterGenerateMethodCall($node, $scope)) {
            return [];
        }

        $controllerClassReflection = $this->matchControllerFirstArgClassReflection($node, $scope);

        // the used argument is not a class reference
        if (! $controllerClassReflection instanceof ClassReflection) {
            return [];
        }

        $invokeClassMethodReflection = InvokeClassMethodResolver::resolve($controllerClassReflection);

        // there must be __invoke() method
        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(SymfonyRuleIdentifier::REQUIRE_ROUTE_NAME_TO_GENERATE_CONTROLLER_ROUTE)
            ->build();

        if (! $invokeClassMethodReflection instanceof ReflectionMethod) {
            return [$identifierRuleError];
        }

        $routeAttributes = $this->findRouteAttributes($invokeClassMethodReflection);
        if ($this->hasAtLeastOneRouteWithSelfClassName($routeAttributes, $controllerClassReflection)) {
            return [];
        }

        return [$identifierRuleError];
    }

    private function isRouterGenerateMethodCall(MethodCall $methodCall, Scope $scope): bool
    {
        if ($methodCall->isFirstClassCallable()) {
            return false;
        }

        if (! $methodCall->name instanceof Identifier) {
            return false;
        }

        if ($methodCall->name->toString() !== 'generate') {
            return false;
        }

        $callerType = $scope->getType($methodCall->var);
        if (! $callerType instanceof ObjectType) {
            return false;
        }

        return $callerType->isInstanceOf(SymfonyClass::URL_GENERATOR)->yes();
    }

    private function matchControllerFirstArgClassReflection(MethodCall $methodCall, Scope $scope): ?ClassReflection
    {
        $firstArg = $methodCall->getArgs()[0];
        $argType = $scope->getType($firstArg->value);

        // we look for a controller class reference
        if (! $argType instanceof ConstantStringType) {
            return null;
        }

        $controllerClass = $argType->getValue();
        if (! $this->reflectionProvider->hasClass($controllerClass)) {
            return null;
        }

        return $this->reflectionProvider->getClass($controllerClass);
    }

    /**
     * @return ReflectionAttribute[]
     */
    private function findRouteAttributes(ReflectionMethod $reflectionMethod): array
    {
        return array_merge(
            method_exists($reflectionMethod, 'getAttributes') ? $reflectionMethod->getAttributes(SymfonyClass::ROUTE_ATTRIBUTE) : [],
            method_exists($reflectionMethod, 'getAttributes') ? $reflectionMethod->getAttributes(SymfonyClass::ROUTE_ANNOTATION) : []
        );
    }

    /**
     * @param ReflectionAttribute[] $routeAttributes
     */
    private function hasAtLeastOneRouteWithSelfClassName(array $routeAttributes, ClassReflection $classReflection): bool
    {
        foreach ($routeAttributes as $routeAttribute) {
            $routeName = $routeAttribute->getArguments()['name'] ?? null;

            // name must be same as current controller class
            if ($routeName === $classReflection->getName()) {
                return true;
            }
        }

        return false;
    }
}
