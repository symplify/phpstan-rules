<?php

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenStaticClassConstFetchRule\Fixture;

class ParentWithOverriddenConst
{
    protected const SOME_CONST = 'parent_value';

    public function run()
    {
        return static::SOME_CONST;
    }
}

class ChildOverridingConst extends ParentWithOverriddenConst
{
    protected const SOME_CONST = 'child_value';
}
