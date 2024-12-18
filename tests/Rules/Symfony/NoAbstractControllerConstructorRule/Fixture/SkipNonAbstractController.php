<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoAbstractControllerConstructorRule\Fixture;

final class SkipNonAbstractController
{
    public function __construct()
    {
    }
}
