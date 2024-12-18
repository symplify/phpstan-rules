<?php

namespace Symplify\PHPStanRules\Tests\PHPStan\Rule\SingleArgEventDispatchRule\Fixture;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SkipSingleDispatch
{
    public function run(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->dispatch('one');
    }
}
