<?php

namespace Doctrine\ORM\Mapping;

if (class_exists('Doctrine\ORM\Mapping\ManyToMany')) {
    return;
}

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class ManyToMany
{
    public function __construct(?string $targetEntity = null)
    {
    }
}
