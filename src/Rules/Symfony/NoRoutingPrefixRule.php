<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use Symplify\PHPStanRules\Enum\ClassName;
use Symplify\PHPStanRules\Enum\SymfonyRuleIdentifier;

/**
 * @implements Rule<MethodCall>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoRoutingPrefixRule\NoRoutingPrefixRuleTest
 */
final class NoRoutingPrefixRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid global route prefixing, to use single place for paths and improve static analysis';

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

        $methodName = $node->name->toString();
        if ($methodName !== 'prefix') {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if (! $callerType instanceof ObjectType) {
            return [];
        }

        if (! $callerType->isInstanceOf(ClassName::SYMFONY_ROUTE_IMPORT_CONFIGURATOR)->yes()) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->line($node->getStartLine())
            ->identifier(SymfonyRuleIdentifier::NO_ROUTING_PREFIX)
            ->build();

        return [$ruleError];
    }
}
