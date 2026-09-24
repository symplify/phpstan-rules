<?php

namespace Symfony\Component\Mailer;

if (class_exists('Symfony\Component\Mailer\Transport')) {
    return;
}

final class Transport implements TransportInterface
{
}
