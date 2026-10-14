<?php

namespace Doctrine\Persistence;

if (interface_exists('Doctrine\Persistence\ObjectRepository')) {
    return;
}

interface ObjectRepository
{
}
