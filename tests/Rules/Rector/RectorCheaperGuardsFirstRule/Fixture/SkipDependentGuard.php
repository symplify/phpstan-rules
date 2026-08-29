<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\RectorCheaperGuardsFirstRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;

final class SkipDependentGuard extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    public function refactor(Node $node): ?Node
    {
        $type = $this->getType($node);

        // value-returning guard in between -> reordering is unsafe, must NOT report
        if ($type->isString()->yes()) {
            return $node;
        }

        // depends on $type -> must NOT report
        if ($type->isInteger()->yes()) {
            return null;
        }

        return $node;
    }
}
