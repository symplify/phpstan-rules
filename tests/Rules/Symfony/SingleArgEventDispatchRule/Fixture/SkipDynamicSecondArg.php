<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\SingleArgEventDispatchRule\Fixture;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SkipDynamicSecondArg
{
    public function run(EventDispatcherInterface $eventDispatcher, object $event, string $name)
    {
        $eventDispatcher->dispatch($event, $name);
    }
}
