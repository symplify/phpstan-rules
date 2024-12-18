<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\SingleArgEventDispatchRule\Fixture;

use TomasVotruba\Handyman\Tests\PHPStan\Rule\SingleArgEventDispatchRule\Source\NotEventDispatcher;

final class SkipUnrelatedDispatch
{
    public function run(NotEventDispatcher $eventDispatcher)
    {
        $eventDispatcher->dispatch('one', 'two');
    }
}
