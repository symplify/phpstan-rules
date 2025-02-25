<?php

namespace Doctrine\ORM;

if (class_exists('Doctrine\ORM\EntityManagerInterface')) {
    return;
}

interface EntityManagerInterface
{
    public function getRepository(string $class): object;
}
