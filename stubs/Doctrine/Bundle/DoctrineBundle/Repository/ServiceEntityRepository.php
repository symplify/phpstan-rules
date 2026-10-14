<?php

namespace Doctrine\Bundle\DoctrineBundle\Repository;

use Doctrine\Persistence\ObjectRepository;

if (class_exists('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository')) {
    return;
}

abstract class ServiceEntityRepository implements ObjectRepository
{
}
