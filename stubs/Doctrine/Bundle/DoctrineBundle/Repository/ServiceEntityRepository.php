<?php

namespace Doctrine\Bundle\DoctrineBundle\Repository;

if (class_exists('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository')) {
    return;
}

abstract class ServiceEntityRepository
{
}
