<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\SingleArgEventDispatchRule\Fixture;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ReportEventDispatcher
{
    public function run(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->dispatch('one', 'two');
    }
}
