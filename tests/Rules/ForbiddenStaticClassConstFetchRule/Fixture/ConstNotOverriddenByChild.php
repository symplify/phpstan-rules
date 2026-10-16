<?php

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenStaticClassConstFetchRule\Fixture;

class ParentWithoutOverriddenConst
{
    protected const SOME_CONST = 'parent_value';

    public function run()
    {
        return static::SOME_CONST;
    }
}

class ChildWithoutConstOverride extends ParentWithoutOverriddenConst
{
    public function other()
    {
    }
}
