<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoRequiredOutsideClassRule\Fixture;

trait TraitWithRequire
{
    /**
     * @required
     */
    public function inject()
    {
    }
}
