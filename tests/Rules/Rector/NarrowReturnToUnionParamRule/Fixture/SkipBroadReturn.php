<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NarrowReturnToUnionParamRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;

final class SkipBroadReturn
{
    public function doesNotReturnParam(New_|MethodCall $node): ?Node
    {
        if ($node instanceof New_) {
            return null;
        }

        return null;
    }

    public function scalarUnion(int|float $value): int|float
    {
        return $value;
    }

    public function singleClassParam(New_ $node): ?Node
    {
        return $node;
    }

    public function alreadyExplicitUnionReturn(New_|MethodCall $node): New_|MethodCall|null
    {
        return $node;
    }

    public function returnTypeIsUnionMember(ArrayDimFetch|Variable $expr): ?Variable
    {
        while ($expr instanceof ArrayDimFetch) {
            $expr = $expr->var;
        }

        if (! $expr instanceof Variable) {
            return null;
        }

        return $expr;
    }
}
