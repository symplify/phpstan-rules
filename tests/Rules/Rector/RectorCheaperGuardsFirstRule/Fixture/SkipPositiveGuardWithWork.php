<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\RectorCheaperGuardsFirstRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;

final class SkipPositiveGuardWithWork extends AbstractRector
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

        if ($this->isName($node, 'array')) {
            $node->setAttribute('marker', true);
        }

        // real work on every path, so the getType() is not wasted when the cheap guard fails
        return $node;
    }
}
