<?php

namespace Symplify\PHPStanRules\Tests\Rules\StringFileAbsolutePathExistsRule\Fixture;

final class NonExistingFile
{
    public function go()
    {
        return __DIR__  . '/some_file.yml';
    }
}
