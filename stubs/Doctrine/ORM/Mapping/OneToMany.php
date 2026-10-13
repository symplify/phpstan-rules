<?php

namespace Doctrine\ORM\Mapping;

if (class_exists('Doctrine\ORM\Mapping\OneToMany')) {
    return;
}

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class OneToMany
{
    public function __construct(?string $targetEntity = null)
    {
    }
}
