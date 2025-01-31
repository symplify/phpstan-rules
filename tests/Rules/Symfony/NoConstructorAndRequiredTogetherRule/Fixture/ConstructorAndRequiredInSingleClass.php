<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoConstructorAndRequiredTogetherRule\Fixture;

final class ConstructorAndRequiredInSingleClass
{
    public function __construct()
    {
    }

    /**
     * @required
     */
    public function someRequired()
    {

    }
}
