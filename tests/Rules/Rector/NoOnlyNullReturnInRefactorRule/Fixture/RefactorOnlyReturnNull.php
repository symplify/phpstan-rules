<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoOnlyNullReturnInRefactorRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\If_;
use Rector\Rector\AbstractRector;

final class RefactorOnlyReturnNull extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [];
    }

    public function refactor(Node $node)
    {
        if ($node instanceof If_) {
            return null;
        }

        return null;
    }
}
