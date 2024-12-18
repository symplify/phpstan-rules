<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoAbstractControllerConstructorRule\Fixture;

abstract class SomeAbstractController
{
    public function __construct()
    {
    }
}
