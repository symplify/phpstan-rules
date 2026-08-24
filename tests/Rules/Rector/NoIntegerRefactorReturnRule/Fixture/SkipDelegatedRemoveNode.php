<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoIntegerRefactorReturnRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitor;
use Rector\Rector\AbstractRector;

final class SkipDelegatedRemoveNode extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): null|int
    {
        return $this->refactorClass($node);
    }

    private function refactorClass(Node $node): null|int
    {
        if ($node instanceof Class_) {
            return NodeVisitor::REMOVE_NODE;
        }

        return null;
    }
}
