<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

final class HasScopedReturnNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private ?Expr $returnExpr = null
    ) {
    }

    public function enterNode(Node $node): int|Node|null
    {
        if ($node instanceof Closure) {
            return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
        }

        if (! $node instanceof Return_) {
            return null;
        }

        if (! $node->expr instanceof Expr) {
            return null;
        }

        $this->returnExpr = $node->expr;
        return $node;
    }

    public function hasReturn(): bool
    {
        return $this->returnExpr !== null;
    }

    public function getReturnExpr(): ?Expr
    {
        return $this->returnExpr;
    }
}
