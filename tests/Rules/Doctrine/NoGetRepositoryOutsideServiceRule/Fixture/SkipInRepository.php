<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoGetRepositoryOutsideServiceRule\Fixture;

use Doctrine\ORM\EntityManager;
use TomasVotruba\Handyman\Tests\PHPStan\Rule\NoGetRepositoryOutsideServiceRule\Source\SomeRandomEntity;

final readonly class SkipInRepository
{
    public function __construct(
        private EntityManager $entityManager
    ) {
        $someRepository = $this->entityManager->getRepository(SomeRandomEntity::class);
    }
}
