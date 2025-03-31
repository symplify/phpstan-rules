<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class DoctrineClass
{
    /**
     * @var string
     */
    public const ODM_SERVICE_REPOSITORY = 'Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository';

    /**
     * @var string
     */
    public const ODM_SERVICE_REPOSITORY_INTERFACE = 'Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepositoryInterface';

    /**
     * @var string
     */
    public const ORM_SERVICE_REPOSITORY = 'Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository';

    /**
     * @var string
     */
    public const FIXTURE_INTERFACE = 'Doctrine\Common\DataFixtures\FixtureInterface';

    /**
     * @var string
     */
    public const ENTITY_REPOSITORY = 'Doctrine\ORM\EntityRepository';

    /**
     * @var string
     */
    public const CONNECTION = 'Doctrine\DBAL\Connection';
}
