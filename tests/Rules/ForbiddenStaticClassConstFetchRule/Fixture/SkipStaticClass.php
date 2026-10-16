<?php

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenStaticClassConstFetchRule\Fixture;

class SkipStaticClass
{
    public function run()
    {
        return static::class;
    }
}
