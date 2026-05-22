<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class DoctrineEvents
{
    /**
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/events.html
     * @var mixed[]
     */
    public const ORM_LIST = [
        'preRemove',
        'postRemove',
        'prePersist',
        'postPersist',
        'preUpdate',
        'postUpdate',
        'postLoad',
        'loadClassMetadata',
        'onClassMetadataNotFound',
        'preFlush',
        'onFlush',
        'postFlush',
        'onClear',
    ];

    /**
     * @see https://www.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/events.html#lifecycle-events
     * @var mixed[]
     */
    public const ODM_LIST = [
        'documentNotFound',
        'onClear',
        'postCollectionLoad',
    ];
}
