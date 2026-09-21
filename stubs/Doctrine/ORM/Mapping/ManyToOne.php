<?php

namespace Doctrine\ORM\Mapping;

if (class_exists('Doctrine\ORM\Mapping\ManyToOne')) {
    return;
}

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class ManyToOne
{
    public function __construct(?string $targetEntity = null)
    {
    }
}
