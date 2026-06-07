<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Source\SomeEntity;
use Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Source\SomeEventDispatcher;

final class SkipNotSetterCall
{
    public function first()
    {
        $someEventDispatcher = new SomeEventDispatcher();
        $someEventDispatcher->dispatch('event.name');
    }

    public function second()
    {
        $someEventDispatcher = new SomeEventDispatcher();
        $someEventDispatcher->dispatch('another.name');
    }
}
