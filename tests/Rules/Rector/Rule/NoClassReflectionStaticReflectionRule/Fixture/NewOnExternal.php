<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoClassReflectionStaticReflectionRule\Fixture;

use ReflectionClass;

final class NewOnExternal
{
    public function run(): object
    {
        return new ReflectionClass('SomeType');
    }
}
