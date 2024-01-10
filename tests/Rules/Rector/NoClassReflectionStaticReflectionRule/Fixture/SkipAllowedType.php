<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoClassReflectionStaticReflectionRule\Fixture;

use ReflectionClass;

final class SkipAllowedType
{
    public function check(): object
    {
        return new ReflectionClass(\PhpParser\Node::class);
    }
}
