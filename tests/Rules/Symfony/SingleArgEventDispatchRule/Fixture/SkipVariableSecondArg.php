<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\SingleArgEventDispatchRule\Fixture;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SkipVariableSecondArg
{
    public function run(EventDispatcherInterface $eventDispatcher, object $event)
    {
        $eventName = 'some_event';
        $eventDispatcher->dispatch($event, $eventName);
    }
}
