<?php

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingVariableDimFetchRule\Fixture;

final class SkipDefinedVariable
{
    public function some()
    {
        $dim = [];
        $dim['key'] = 'value';
    }
}
