<?php

namespace Symfony\Component\Console\Attribute;

if (class_exists('Symfony\Component\Console\Attribute\AsCommand')) {
    return;
}

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsCommand
{
    public function __construct(string $name, ?string $description = null)
    {
    }
}
