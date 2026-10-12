<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\PreferGenericTemplateOnSameUnionTypeRule\Fixture;

use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;

final class SameUnionParamAndReturn
{
    public function process(ClassMethod|Function_ $node): ClassMethod|Function_|null
    {
        return $node;
    }
}
