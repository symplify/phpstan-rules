<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class DoctrineClass
{
    public const string ODM_SERVICE_REPOSITORY = 'Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository';

    public const string ODM_SERVICE_REPOSITORY_INTERFACE = 'Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepositoryInterface';

    public const string ORM_SERVICE_REPOSITORY = 'Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository';

    public const string FIXTURE_INTERFACE = 'Doctrine\Common\DataFixtures\FixtureInterface';

    public const string ENTITY_REPOSITORY = 'Doctrine\ORM\EntityRepository';

    public const string CONNECTION = 'Doctrine\DBAL\Connection';

    public const string DOCUMENT_REPOSITORY = 'Doctrine\ODM\MongoDB\Repository\DocumentRepository';
}
