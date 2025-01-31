<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoConstructorAndRequiredTogetherRule\Fixture;

final class SkipSoleMethod
{
    public function __construct()
    {
    }
}
