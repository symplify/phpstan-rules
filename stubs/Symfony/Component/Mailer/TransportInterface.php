<?php

namespace Symfony\Component\Mailer;

if (interface_exists('Symfony\Component\Mailer\TransportInterface')) {
    return;
}

interface TransportInterface
{
}
