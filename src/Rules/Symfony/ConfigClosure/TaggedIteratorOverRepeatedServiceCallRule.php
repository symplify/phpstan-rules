<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony\ConfigClosure;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyClosureDetector;
use Symplify\PHPStanRules\Symfony\NodeFinder\RepeatedServiceAdderCallNameFinder;

/**
 * @implements Rule<Closure>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\TaggedIteratorOverRepeatedServiceCallRule\TaggedIteratorOverRepeatedServiceCallRuleTest
 */
final class TaggedIteratorOverRepeatedServiceCallRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of repeated "->call(%s, ...)" calls, pass services as tagged iterator argument to the constructor';

    /**
     * @var string
     */
    private const RULE_IDENTIFIER = 'symfony.taggedIteratorOverRepeatedServiceCall';

    public function getNodeType(): string
    {
        return Closure::class;
    }

    /**
     * @param Closure $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! SymfonyClosureDetector::detect($node)) {
            return [];
        }

        $ruleErrors = [];

        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof Expression) {
                continue;
            }

            $nestedExpr = $stmt->expr;
            if (! $nestedExpr instanceof MethodCall) {
                continue;
            }

            if ($nestedExpr->isFirstClassCallable()) {
                continue;
            }

            $adderCallName = RepeatedServiceAdderCallNameFinder::find($nestedExpr);
            if (! is_string($adderCallName)) {
                continue;
            }

            $ruleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $adderCallName))
                ->identifier(self::RULE_IDENTIFIER)
                ->line($stmt->getStartLine())
                ->build();

            $ruleErrors[] = $ruleError;
        }

        return $ruleErrors;
    }
}
