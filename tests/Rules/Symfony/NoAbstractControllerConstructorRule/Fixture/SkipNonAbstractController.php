<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoAbstractControllerConstructorRule\Fixture;

final class SkipNonAbstractController
{
    public function __construct()
    {
    }
}
