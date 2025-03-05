<?php

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoProtectedClassStmtRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\NoProtectedClassStmtRule\Source\ParentClassWithMethod;

final class SkipParentRequired extends ParentClassWithMethod
{
    protected function some()
    {
    }
}
