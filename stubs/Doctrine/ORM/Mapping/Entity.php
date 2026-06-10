<?php

namespace Doctrine\ORM\Mapping;

if (class_exists('Doctrine\ORM\Mapping\Entity')) {
    return;
}

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Entity
{
}
