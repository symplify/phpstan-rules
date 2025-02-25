<?php

namespace Doctrine\ODM\MongoDB;

if (class_exists('Doctrine\ODM\MongoDB\DocumentManager')) {
    return;
}

abstract class DocumentManager
{
    public function getRepository(string $class)
    {
    }
}
