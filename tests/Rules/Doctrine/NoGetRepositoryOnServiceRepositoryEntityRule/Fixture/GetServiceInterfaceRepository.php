<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Source\Entity\SomeEntity;

final class GetServiceInterfaceRepository
{
    public function run(EntityManagerInterface $entityManager)
    {
        $someRepository = $entityManager->getRepository(SomeEntity::class);
    }
}
