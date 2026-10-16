<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Rector\PHPStan\ScopeFetcher;

/**
 * Flags ScopeFetcher::fetch() called right before a cheap guard that does not use the scope.
 * The guard should run first, so most nodes bail out before scope resolution.
 *
 * @implements Rule<ClassMethod>
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\NoScopeFetchBeforeCheapGuardRule\NoScopeFetchBeforeCheapGuardRuleTest
 */
final class NoScopeFetchBeforeCheapGuardRule implements Rule
{
    public const ERROR_MESSAGE = 'Move the "return null" guard above ScopeFetcher::fetch(), as it does not use the scope - most nodes then bail out before scope resolution.';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->name->toString() !== 'refactor') {
            return [];
        }

        $stmts = $node->stmts;
        if ($stmts === null) {
            return [];
        }

        foreach ($stmts as $key => $stmt) {
            $scopeVariableName = $this->matchScopeFetchVariableName($stmt);
            if ($scopeVariableName === null) {
                continue;
            }

            $nextStmt = $stmts[$key + 1] ?? null;
            if (! $nextStmt instanceof If_) {
                continue;
            }

            if (! $this->isReturnNullGuard($nextStmt)) {
                continue;
            }

            if ($this->usesVariable($nextStmt->cond, $scopeVariableName)) {
                continue;
            }

            return [
                RuleErrorBuilder::message(self::ERROR_MESSAGE)
                    ->identifier('rector.scopeFetchBeforeCheapGuard')
                    ->line($stmt->getStartLine())
                    ->build(),
            ];
        }

        return [];
    }

    private function matchScopeFetchVariableName(Stmt $stmt): ?string
    {
        if (! $stmt instanceof Expression) {
            return null;
        }

        if (! $stmt->expr instanceof Assign) {
            return null;
        }

        $assign = $stmt->expr;
        if (! $assign->var instanceof Variable || ! is_string($assign->var->name)) {
            return null;
        }

        if (! $assign->expr instanceof StaticCall) {
            return null;
        }

        $staticCall = $assign->expr;
        if (! $staticCall->class instanceof Name) {
            return null;
        }

        if ($staticCall->class->toString() !== ScopeFetcher::class) {
            return null;
        }

        if (! $staticCall->name instanceof Identifier || $staticCall->name->toString() !== 'fetch') {
            return null;
        }

        return $assign->var->name;
    }

    private function isReturnNullGuard(If_ $if): bool
    {
        if (count($if->stmts) !== 1) {
            return false;
        }

        $onlyStmt = $if->stmts[0];
        if (! $onlyStmt instanceof Return_) {
            return false;
        }

        return ! $onlyStmt->expr instanceof Expr || $this->isNullConstant($onlyStmt->expr);
    }

    private function isNullConstant(Expr $expr): bool
    {
        return $expr instanceof ConstFetch && $expr->name->toLowerString() === 'null';
    }

    private function usesVariable(Expr $expr, string $variableName): bool
    {
        $foundVariable = new NodeFinder()
            ->findFirst(
                [$expr],
                static fn (Node $subNode): bool => $subNode instanceof Variable && $subNode->name === $variableName
            );

        return $foundVariable instanceof Variable;
    }
}
