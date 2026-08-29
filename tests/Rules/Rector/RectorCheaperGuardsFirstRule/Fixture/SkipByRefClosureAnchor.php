<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\RectorCheaperGuardsFirstRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;

final class SkipByRefClosureAnchor extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    public function refactor(Node $node): ?Node
    {
        $hasChanged = false;

        // the expensive call runs inside a closure that mutates $hasChanged by-reference;
        // the anchor is a bare call statement, not a captured value, so hoisting is unsafe
        $this->traverseNodesWithCallable($node, function (Node $subNode) use (&$hasChanged) {
            if ($subNode->getType()->isString()->yes()) {
                $hasChanged = true;
            }

            return null;
        });

        if (! $hasChanged) {
            return null;
        }

        return $node;
    }
}
