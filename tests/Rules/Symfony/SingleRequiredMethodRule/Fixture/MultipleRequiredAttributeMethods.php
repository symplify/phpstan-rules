<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\SingleRequiredMethodRule\Fixture;

use Symfony\Contracts\Service\Attribute\Required;

final class MultipleRequiredAttributeMethods
{
    #[Required]
    public function autowireFirst()
    {
    }

    #[Required]
    public function autowireSecond()
    {
    }
}
