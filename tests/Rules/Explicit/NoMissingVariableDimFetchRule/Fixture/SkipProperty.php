<?php

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingVariableDimFetchRule\Fixture;

final class SkipProperty
{
    private $someProperty = [];

    public function some()
    {
        $this->someProperty['key'] = 'value';
    }
}
