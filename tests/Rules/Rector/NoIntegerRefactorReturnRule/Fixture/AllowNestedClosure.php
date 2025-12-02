<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoIntegerRefactorReturnRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use Rector\Rector\AbstractRector;

final class AllowNestedClosure extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Class_::class, Interface_::class];
    }

    public function refactor(Node $node): \PhpParser\Node|int
    {
        if ($node instanceof Class_) {
            return $node;
        }

        $nodeFinder = new NodeFinder();
        $nodeFinder->find($node, function (Node $subNode) {
            if ($subNode instanceof Node\Stmt\Function_) {
                return NodeTraverser::STOP_TRAVERSAL;
            }

            return true;
        });


        return NodeVisitor::REMOVE_NODE;
    }
}
