<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\PreferGenericTemplateOnSameUnionTypeRule\Fixture;

use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;

final class SkipGenericTemplate
{
    /**
     * @template TNode of ClassMethod|Function_
     * @param TNode $node
     * @return TNode|null
     */
    public function alreadyGeneric(ClassMethod|Function_ $node): ClassMethod|Function_|null
    {
        return $node;
    }

    public function scalarUnion(int|float $value): int|float
    {
        return $value + 1;
    }

    public function mismatchedUnion(ClassMethod|Function_ $node): ClassMethod|Interface_|null
    {
        return null;
    }
}
