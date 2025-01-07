<?php

namespace Doctrine\ODM\MongoDB\Repository;

if (class_exists('Doctrine\ODM\MongoDB\Repository\DocumentRepository')) {
    return;
}

abstract class DocumentRepository
{
}
