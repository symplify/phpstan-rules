<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\RectorCheaperGuardsFirstRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;

final class SkipSideEffectAnchor extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    public function refactor(Node $node): ?Node
    {
        // the anchor block writes attributes for non-substr nodes; the guard cannot hoist above it
        if ($node instanceof CallLike) {
            foreach ($node->getArgs() as $arg) {
                if ($arg->value->getType()->isString()->yes()) {
                    $arg->setAttribute('marked', true);
                }
            }
        }

        if (! $this->isName($node, 'substr')) {
            return null;
        }

        return $node;
    }
}
