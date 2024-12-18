<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoAbstractControllerConstructorRule\Fixture;

abstract class SomeAbstractController
{
    public function __construct()
    {
    }
}
