<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Enum\SymfonyClass;

/**
 * @implements Rule<MethodCall>
 */
final class SingleArgEventDispatchRule implements Rule
{
    public const string ERROR_MESSAGE = 'The event dispatch() method can have only 1 arg - the event object';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier) {
            return [];
        }

        if ($node->name->toString() !== 'dispatch') {
            return [];
        }

        $args = $node->getArgs();

        // all good
        if (count($args) === 1) {
            return [];
        }

        // allow 2nd arg when dynamic, e.g. a variable event name
        if (count($args) === 2) {
            $secondArgType = $scope->getType($args[1]->value);
            if ($secondArgType->getConstantStrings() === []) {
                return [];
            }
        }

        $callerType = $scope->getType($node->var);
        if (! $callerType instanceof ObjectType) {
            return [];
        }

        if (! $callerType->isInstanceOf(SymfonyClass::EVENT_DISPATCHER_INTERFACE)->yes()) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(SymfonyRuleIdentifier::SINGLE_ARG_EVENT_DISPATCH)
            ->build();

        return [$identifierRuleError];
    }
}
