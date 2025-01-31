<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoConstructorAndRequiredTogetherRule\Fixture;

final class SkipOtherSoleMethod
{
    /**
     * @required
     */
    public function autowire()
    {
    }
}
