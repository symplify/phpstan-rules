<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\NodeTraverser\SimpleCallableNodeTraverser;

final class ReturnNodeFinder
{
    /**
     * @var \Symplify\PHPStanRules\NodeTraverser\SimpleCallableNodeTraverser
     */
    private $simpleCallableNodeTraverser;
    public function __construct(SimpleCallableNodeTraverser $simpleCallableNodeTraverser)
    {
        $this->simpleCallableNodeTraverser = $simpleCallableNodeTraverser;
    }

    public function findOnlyReturnsExpr(ClassMethod $classMethod): ?\PhpParser\Node\Expr
    {
        $returns = $this->findReturnsWithValues($classMethod);
        if (count($returns) !== 1) {
            return null;
        }

        $onlyReturn = $returns[0];
        return $onlyReturn->expr;
    }

    /**
     * @return Return_[]
     */
    public function findReturnsWithValues(ClassMethod $classMethod): array
    {
        $returns = [];

        $this->simpleCallableNodeTraverser->traverseNodesWithCallable((array) $classMethod->stmts, static function (
            Node $node
        ) use (&$returns) {
            // skip different scope
            if ($node instanceof FunctionLike) {
                return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }

            if (! $node instanceof Return_) {
                return null;
            }

            if ($node->expr === null) {
                return null;
            }

            $returns[] = $node;
        });

        return $returns;
    }
}
