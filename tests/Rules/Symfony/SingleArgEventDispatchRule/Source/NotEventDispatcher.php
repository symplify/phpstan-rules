<?php

namespace Symplify\PHPStanRules\Tests\PHPStan\Rule\SingleArgEventDispatchRule\Source;

final class NotEventDispatcher
{
    public function dispatch()
    {
        $args = func_get_args();
    }
}
