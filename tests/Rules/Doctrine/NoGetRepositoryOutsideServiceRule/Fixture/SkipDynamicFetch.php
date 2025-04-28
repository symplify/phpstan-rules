<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOutsideServiceRule\Fixture;

use Doctrine\ORM\EntityManager;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOutsideServiceRule\Source\SomeRandomEntity;

final readonly class SkipDynamicFetch
{
    public function run(
        EntityManager $entityManager,
        string $className
    ) {
        $someRepository = $entityManager->getRepository($className);
    }
}
