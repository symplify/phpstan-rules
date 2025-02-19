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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;

/**
 * To pass a controller class in $this->router->generate(SomeController::class),
 * the controller must be present #[Route(name:: self::class)
 *
 * @see https://symfony.com/blog/new-in-symfony-6-4-fqcn-based-routes
 *
 * @implements Rule<MethodCall>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\RouteGenerateControllerClassRequireNameRule\RouteGenerateControllerClassRequireNameRuleTest
 */
final readonly class RouteGenerateControllerClassRequireNameRule implements Rule
{
    /**
     * @api
     * @var string
     */
    public const ERROR_MESSAGE = 'To pass a controller class to generate() method, the controller must have "#[Route(name: self::class)]" above the __invoke() method';

    /**
     * @var string
     */
    private const IDENTIFIER = 'be5.routeGenerateControllerName';

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
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

        $invokeClassMethodReflection = \Symplify\PHPStanRules\Reflection\InvokeClassMethodResolver::resolve($controllerClassReflection);

        // there must be __invoke() method
        if (! $invokeClassMethodReflection instanceof ReflectionMethod) {
            return [RuleErrorBuilder::message(self::ERROR_MESSAGE)->identifier(self::IDENTIFIER)->build()];
        }

        $routeAttributes = $this->findRouteAttributes($invokeClassMethodReflection);
        if ($this->hasAtLeastOneRouteWithSelfClassName($routeAttributes, $controllerClassReflection)) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)->identifier(self::IDENTIFIER)->build()];
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

        return $callerType->isInstanceOf(UrlGeneratorInterface::class)->yes();
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
            $reflectionMethod->getAttributes(\Symfony\Component\Routing\Attribute\Route::class),
            $reflectionMethod->getAttributes(Route::class),
            $reflectionMethod->getAttributes(\Symfony\Component\Routing\Annotation\Route::class)
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
