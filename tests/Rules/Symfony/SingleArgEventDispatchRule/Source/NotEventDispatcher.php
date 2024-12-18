<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\SingleArgEventDispatchRule\Source;

final class NotEventDispatcher
{
    public function dispatch()
    {
        $args = func_get_args();
    }
}
