<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use TomasVotruba\Handyman\Enum\ClassName;

/**
 * @implements Rule<MethodCall>
 */
final class SingleArgEventDispatchRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The event dispatch() method can have only 1 arg - the event object';

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
        if (! $node->name instanceof Identifier) {
            return [];
        }

        if ($node->name->toString() !== 'dispatch') {
            return [];
        }

        // all good
        if (count($node->getArgs()) === 1) {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if (! $callerType instanceof ObjectType) {
            return [];
        }

        if (! $callerType->isInstanceOf(ClassName::EVENT_DISPATCHER_INTERFACE)->yes()) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)->build();
        return [$ruleError];
    }
}
