<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use Symplify\PHPStanRules\Enum\SymfonyClass;
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

        if ($node->name->toString() !== 'prefix') {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if (! $callerType instanceof ObjectType) {
            return [];
        }

        if (! $callerType->isInstanceOf(SymfonyClass::ROUTE_IMPORT_CONFIGURATOR)->yes()) {
            return [];
        }

        if ($this->isAllowedExternalBundleImport($node)) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->line($node->getStartLine())
            ->identifier(SymfonyRuleIdentifier::NO_ROUTING_PREFIX)
            ->build();

        return [$ruleError];
    }

    private function isAllowedExternalBundleImport(MethodCall $methodCall): bool
    {
        if (! $methodCall->var instanceof MethodCall) {
            return false;
        }

        $parentCaller = $methodCall->var;
        if (! $parentCaller->name instanceof Identifier || $parentCaller->name->toString() !== 'import') {
            return false;
        }

        $importArgPath = $parentCaller->getArgs()[0]->value;
        if (! $importArgPath instanceof String_) {
            return false;
        }

        // these external bundles are typically prefixed on purpose
        if (strncmp($importArgPath->value, '@FrameworkBundle', strlen('@FrameworkBundle')) === 0) {
            return true;
        }

        return strncmp($importArgPath->value, '@WebProfilerBundle', strlen('@WebProfilerBundle')) === 0;
    }
}
