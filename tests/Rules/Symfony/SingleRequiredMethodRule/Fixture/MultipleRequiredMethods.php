<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\SingleRequiredMethodRule\Fixture;

final class MultipleRequiredMethods
{
    /**
     * @required
     */
    public function autowireFirst()
    {
    }

    /**
     * @required
     */
    public function autowireSecond()
    {
    }
}
