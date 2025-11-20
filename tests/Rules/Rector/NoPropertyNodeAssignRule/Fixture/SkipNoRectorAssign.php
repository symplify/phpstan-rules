<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoPropertyNodeAssignRule\Fixture;

use PhpParser\Node;

final class SkipNoRectorAssign
{
    private ?\PhpParser\Node $localNode = null;

    public function refactor(Node $node)
    {
        $this->localNode = $node;
    }
}
