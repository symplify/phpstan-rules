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
    private bool $hasReturn = false;
    public function __construct(bool $hasReturn = false)
    {
        $this->hasReturn = $hasReturn;
    }

    /**
     * @return int|\PhpParser\Node|null
     */
    public function enterNode(Node $node)
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

        $this->hasReturn = true;
        return $node;
    }

    public function hasReturn(): bool
    {
        return $this->hasReturn;
    }
}
