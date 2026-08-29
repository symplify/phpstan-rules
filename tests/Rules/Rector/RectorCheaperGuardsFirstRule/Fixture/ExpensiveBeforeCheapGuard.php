<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\RectorCheaperGuardsFirstRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;

final class ExpensiveBeforeCheapGuard extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    public function refactor(Node $node): ?Node
    {
        $type = $this->getType($node);
        if ($type->isString()->yes()) {
            return null;
        }

        // cheap, independent of $type, but runs after the expensive getType()
        if (! $this->isName($node, 'array')) {
            return null;
        }

        return $node;
    }
}
