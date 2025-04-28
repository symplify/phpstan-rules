<?php

namespace Doctrine\ORM;

if (class_exists('Doctrine\ORM\EntityManagerInterface')) {
    return;
}

interface EntityManagerInterface
{
    /**
     * @param class-string $class
     * @return EntityRepository
     */
    public function getRepository(string $class): object;

    public function createQueryBuilder();
}
