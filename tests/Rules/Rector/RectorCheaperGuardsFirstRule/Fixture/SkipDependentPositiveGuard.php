<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\RectorCheaperGuardsFirstRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;

final class SkipDependentPositiveGuard extends AbstractRector
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

        // depends on $type, cannot be hoisted above getType()
        if ($type->isInteger()->yes()) {
            return $node;
        }

        return null;
    }
}
