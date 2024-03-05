<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\FirstClassCallables;

final class SomeCalledMethod
{
    public function callMe($number)
    {
    }
}
