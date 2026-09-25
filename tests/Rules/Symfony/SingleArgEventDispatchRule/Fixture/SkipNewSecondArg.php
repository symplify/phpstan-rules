<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\SingleArgEventDispatchRule\Fixture;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SkipNewSecondArg
{
    public function run(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->dispatch('one', new \stdClass());
    }
}
