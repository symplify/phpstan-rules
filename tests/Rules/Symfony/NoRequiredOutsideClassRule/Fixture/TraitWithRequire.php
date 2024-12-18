<?php

namespace Symplify\PHPStanRules\Tests\PHPStan\Rule\NoRequiredOutsideClassRule\Fixture;

trait TraitWithRequire
{
    /**
     * @required
     */
    public function inject()
    {
    }
}
