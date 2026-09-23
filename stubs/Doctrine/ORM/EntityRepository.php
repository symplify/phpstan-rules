<?php

namespace Doctrine\ORM;

if (class_exists('Doctrine\ORM\EntityRepository')) {
    return;
}

/**
 * @template T of object
 */
class EntityRepository
{
    public function createQueryBuilder(string $alias, ?string $indexBy = null): QueryBuilder
    {
    }
}
