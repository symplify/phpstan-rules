<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\SingleArgEventDispatchRule\Fixture;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SkipStringArgumentSecondArg
{
    public function run(EventDispatcherInterface $eventDispatcher, object $event, string $eventName)
    {
        $eventDispatcher->dispatch($event, $eventName);
    }
}
