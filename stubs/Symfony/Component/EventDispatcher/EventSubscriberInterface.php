<?php

namespace Symfony\Component\EventDispatcher;

if (interface_exists('Symfony\Contracts\EventDispatcher\EventSubscriberInterface')) {
    return;
}

interface EventSubscriberInterface
{
}
