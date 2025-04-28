<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOutsideServiceRule\Fixture;

use Doctrine\ORM\EntityManager;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOutsideServiceRule\Source\SomeRandomEntity;

final readonly class SkipDynamicClassConstFetch
{
    public function run(
        EntityManager $entityManager,
        object $someObject
    ) {
        $someRepository = $entityManager->getRepository($someObject::class);
    }
}
