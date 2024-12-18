<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOutsideServiceRule\Fixture;

use Doctrine\ORM\EntityManager;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOutsideServiceRule\Source\SomeRandomEntity;

final readonly class SkipInRepository
{
    public function __construct(
        private EntityManager $entityManager
    ) {
        $someRepository = $this->entityManager->getRepository(SomeRandomEntity::class);
    }
}
