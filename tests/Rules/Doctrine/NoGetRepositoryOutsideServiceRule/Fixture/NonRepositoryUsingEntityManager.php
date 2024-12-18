<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoGetRepositoryOutsideServiceRule\Fixture;

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
