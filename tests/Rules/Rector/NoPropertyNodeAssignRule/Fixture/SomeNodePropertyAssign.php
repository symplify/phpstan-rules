<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoPropertyNodeAssignRule\Fixture;

use PhpParser\Node;
use Rector\Rector\AbstractRector;

final class SomeNodePropertyAssign extends AbstractRector
{
    private ?\PhpParser\Node $localNode = null;

    public function getNodeTypes(): array
    {
    }

    public function refactor(Node $node)
    {
        $this->localNode = $node;
    }
}
