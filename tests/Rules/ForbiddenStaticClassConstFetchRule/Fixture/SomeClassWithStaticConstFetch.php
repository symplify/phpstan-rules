<?php

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenStaticClassConstFetchRule\Fixture;

class SomeClassWithStaticConstFetch
{
    protected const SOME_CONST = 'some_const';

    public function run()
    {
        return static::SOME_CONST;
    }
}
