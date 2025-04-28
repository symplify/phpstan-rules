<?php

namespace Doctrine\ODM\MongoDB;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Doctrine\ODM\MongoDB\Repository\GridFSRepository;
use Doctrine\ODM\MongoDB\Repository\ViewRepository;

if (class_exists('Doctrine\ODM\MongoDB\DocumentManager')) {
    return;
}

abstract class DocumentManager
{
    /**
     * Gets the repository for a document class.
     *
     * @param string $className The name of the Document.
     * @psalm-param class-string<T> $className
     *
     * @return DocumentRepository|GridFSRepository|ViewRepository  The repository.
     * @psalm-return DocumentRepository<T>|GridFSRepository<T>|ViewRepository<T>
     *
     * @template T of object
     */
    public function getRepository(string $class)
    {
    }
}
