<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyClosureDetector;

/**
 * @implements Rule<Closure>
 */
final class NoServiceAutowireDuplicateRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Service autowire() is called as duplicate of $services->defaults()->autowire(). Remove it on the service';

    public function getNodeType(): string
    {
        return Closure::class;
    }

    /**
     * @param Closure $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! SymfonyClosureDetector::detect($node)) {
            return [];
        }

        $ruleErrors = [];

        $hasDefaultsAutowire = false;

        foreach ($node->stmts as $stmt) {
            if ($this->hasAutowireDefaultsMethodCall($stmt)) {
                $hasDefaultsAutowire = true;
                continue;
            }

            if (! $hasDefaultsAutowire) {
                continue;
            }

            $serviceAutowireMethodCall = $this->matchServiceAutowireMethodCall($stmt);
            if (! $serviceAutowireMethodCall instanceof MethodCall) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($serviceAutowireMethodCall->getLine())
                ->identifier(SymfonyRuleIdentifier::RULE_IDENTIFIER)
                ->build();
        }

        return $ruleErrors;
    }

    private function hasAutowireDefaultsMethodCall(Node $someNode): bool
    {
        $nodeFinder = new NodeFinder();

        $autowireDefaultsMethodCall = $nodeFinder->findFirst($someNode, function (Node $node): bool {
            if (! $node instanceof MethodCall) {
                return false;
            }

            if (! NamingHelper::isName($node->name, 'autowire')) {
                return false;
            }

            if (! $node->var instanceof MethodCall) {
                return false;
            }

            // dummy way to detect, @todo improve with possible type check
            return NamingHelper::isName($node->var->name, 'defaults');
        });

        return $autowireDefaultsMethodCall instanceof MethodCall;
    }

    private function matchServiceAutowireMethodCall(Node $someNode): ?MethodCall
    {
        $nodeFinder = new NodeFinder();

        $foundNode = $nodeFinder->findFirst($someNode, function (Node $node): bool {
            if (! $node instanceof MethodCall) {
                return false;
            }

            if (! NamingHelper::isName($node->name, 'autowire')) {
                return false;
            }

            if ($node->getArgs() === []) {
                return true;
            }

            $firstArg = $node->getArgs()[0];
            if (! $firstArg->value instanceof ConstFetch) {
                return false;
            }

            return $firstArg->value->name->toLowerString() === 'true';
        });

        /** @var MethodCall|null $foundNode */
        return $foundNode;
    }
}
