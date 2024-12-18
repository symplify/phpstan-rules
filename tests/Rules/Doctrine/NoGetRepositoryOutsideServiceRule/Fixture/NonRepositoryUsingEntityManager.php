<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOutsideServiceRule\Fixture;

use Doctrine\ORM\EntityManager;

final readonly class NonRepositoryUsingEntityManager
{
    public function __construct(
        private EntityManager $entityManager
    ) {
    }

    public function run(): void
    {
        $someRepository = $this->entityManager->getRepository(self::class);
    }
}
