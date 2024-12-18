<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\SingleArgEventDispatchRule\Fixture;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ReportEventDispatcher
{
    public function run(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->dispatch('one', 'two');
    }
}
