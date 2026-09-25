<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\SingleArgEventDispatchRule\Fixture;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SkipUntypedArgumentSecondArg
{
    public function run(EventDispatcherInterface $eventDispatcher, object $event, $eventName)
    {
        $eventDispatcher->dispatch($event, $eventName);
    }
}
