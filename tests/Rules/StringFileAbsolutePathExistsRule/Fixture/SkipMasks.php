<?php

namespace Symplify\PHPStanRules\Tests\Rules\StringFileAbsolutePathExistsRule\Fixture;

final class SkipMasks
{
    public function go()
    {
        return glob(__DIR__  . '/some_file/*');
    }
}
