<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Fixture;

class SomeClassWithConstructor
{
    public function __construct(string $name)
    {
    }
}
