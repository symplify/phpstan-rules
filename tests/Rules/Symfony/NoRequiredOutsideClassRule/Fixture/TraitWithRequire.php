<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoRequiredOutsideClassRule\Fixture;

trait TraitWithRequire
{
    /**
     * @required
     */
    public function inject()
    {
    }
}
