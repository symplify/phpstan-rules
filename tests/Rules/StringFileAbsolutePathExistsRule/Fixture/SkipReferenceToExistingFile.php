<?php

namespace Symplify\PHPStanRules\Tests\Rules\StringFileAbsolutePathExistsRule\Fixture;

final class SkipReferenceToExistingFile
{
    public function go()
    {
        return __DIR__  . '/../Source/some_file.yml';
    }
}
