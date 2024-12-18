<?php

namespace Symplify\PHPStanRules\Tests\PHPStan\Rule\SingleArgEventDispatchRule\Fixture;

use Symplify\PHPStanRules\Tests\PHPStan\Rule\SingleArgEventDispatchRule\Source\NotEventDispatcher;

final class SkipUnrelatedDispatch
{
    public function run(NotEventDispatcher $eventDispatcher)
    {
        $eventDispatcher->dispatch('one', 'two');
    }
}
