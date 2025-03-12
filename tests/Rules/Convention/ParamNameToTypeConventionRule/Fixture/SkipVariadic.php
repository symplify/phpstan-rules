<?php

namespace Symplify\PHPStanRules\Tests\Rules\Convention\ParamNameToTypeConventionRule\Fixture;

final class SkipVariadic
{
    public function run(...$userId)
    {
    }
}
