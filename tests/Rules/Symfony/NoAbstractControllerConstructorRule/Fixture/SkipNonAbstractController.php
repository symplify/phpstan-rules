<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\PHPStan\Rule\NoAbstractControllerConstructorRule\Fixture;

final class SkipNonAbstractController
{
    public function __construct()
    {
    }
}
