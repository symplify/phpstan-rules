<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoRequiredOutsideClassRule\Fixture;

use Symfony\Contracts\Service\Attribute\Required;

trait TraitWithRequireAttribute
{
    #[Required]
    public function injectAgain()
    {
    }
}
