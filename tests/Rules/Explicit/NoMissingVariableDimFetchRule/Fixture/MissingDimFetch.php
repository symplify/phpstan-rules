<?php

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingVariableDimFetchRule\Fixture;

final class MissingDimFetch
{
    public function some()
    {
        $dim['key'] = 'value';
    }
}
