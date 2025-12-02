<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoIntegerRefactorReturnRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\NodeVisitor;
use Rector\Rector\AbstractRector;

final class AllowRemoveNode extends AbstractRector
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

        return NodeVisitor::REMOVE_NODE;
    }
}
