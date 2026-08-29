<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\RectorCheaperGuardsFirstRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;

final class SkipCheapGuardFirst extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    public function refactor(Node $node): ?Node
    {
        // already cheap-first: nothing to report
        if (! $this->isName($node, 'array')) {
            return null;
        }

        $type = $this->getType($node);
        if ($type->isString()->yes()) {
            return null;
        }

        return $node;
    }
}
