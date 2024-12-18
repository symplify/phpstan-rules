<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoRequiredOutsideClassRule\Fixture;

use Symfony\Contracts\Service\Attribute\Required;

trait TraitWithRequireAttribute
{
    #[Required]
    public function injectAgain()
    {
    }
}
