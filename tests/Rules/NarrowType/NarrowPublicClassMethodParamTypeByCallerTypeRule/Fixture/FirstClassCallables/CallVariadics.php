<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\FirstClassCallables;

use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\FirstClassCallables\SomeCalledMethod;

final class CallVariadics
{
    public function callMe(SomeCalledMethod $someCalledMethod)
    {
        $closure = $someCalledMethod->callMe(...);

        $someCalledMethod->callMe(1000);
    }
}
