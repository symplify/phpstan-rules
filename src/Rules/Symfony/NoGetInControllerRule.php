<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\ClassName;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<MethodCall>
 */
final class NoGetInControllerRule implements Rule
{
    /**
     * @var string[]
     */
    private const CONTROLLER_TYPES = [
        ClassName::SYMFONY_CONTROLLER,
        ClassName::SYMFONY_ABSTRACT_CONTROLLER,
    ];

    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Do not use $this->get(Type::class) method in controller to get services. Use __construct(Type $type) instead';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->isThisGetMethodCall($node)) {
            return [];
        }

        if (! $this->isInControllerClass($scope)) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->file($scope->getFile())
            ->line($node->getStartLine())
            ->identifier(RuleIdentifier::NO_GET_IN_CONTROLLER)
            ->build();

        return [$ruleError];
    }

    private function isInControllerClass(Scope $scope): bool
    {
        if (! $scope->isInClass()) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        foreach (self::CONTROLLER_TYPES as $controllerType) {
            if ($classReflection->isSubclassOf($controllerType)) {
                return true;
            }
        }

        return false;
    }

    private function isThisGetMethodCall(MethodCall $methodCall): bool
    {
        if (! $methodCall->name instanceof Identifier) {
            return false;
        }

        if ($methodCall->name->toString() !== 'get') {
            return false;
        }

        // is "$this"?
        if (! $methodCall->var instanceof Variable) {
            return false;
        }

        if (! is_string($methodCall->var->name)) {
            return false;
        }

        return $methodCall->var->name === 'this';
    }
}
