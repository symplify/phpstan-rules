<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\PreferDirectIsNameRule\Fixture;

use PhpParser\Node;
use Rector\Rector\AbstractRector;

final class SkipDirectIsName extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [];
    }

    public function refactor(Node $node)
    {
        $isName = $this->isName($node, 'test');
    }
}
