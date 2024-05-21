<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Fixture;

final class SkipConstructorOverride extends SomeClassWithConstructor
{
    public function __construct($name)
    {
    }
}
