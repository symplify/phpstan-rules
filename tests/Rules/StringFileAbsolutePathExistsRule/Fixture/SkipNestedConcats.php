<?php

namespace Symplify\PHPStanRules\Tests\Rules\StringFileAbsolutePathExistsRule\Fixture;

final class SkipNestedConcats
{
    public function go()
    {
        return __DIR__  . '/some_file/' . '.yml';
    }
}
