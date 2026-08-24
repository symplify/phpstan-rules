<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoIntegerRefactorReturnRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitor;
use Rector\Rector\AbstractRector;

final class BareIntReturn extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): int
    {
        return NodeVisitor::DONT_TRAVERSE_CHILDREN;
    }
}
