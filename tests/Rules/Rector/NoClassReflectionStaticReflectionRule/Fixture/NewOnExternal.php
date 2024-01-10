<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoClassReflectionStaticReflectionRule\Fixture;

use ReflectionClass;

final class NewOnExternal
{
    public function run(): object
    {
        return new ReflectionClass('SomeType');
    }
}
