<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\SingleArgEventDispatchRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\SingleArgEventDispatchRule\Source\NotEventDispatcher;

final class SkipUnrelatedDispatch
{
    public function run(NotEventDispatcher $eventDispatcher)
    {
        $eventDispatcher->dispatch('one', 'two');
    }
}
