<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Source\NormalEntity;

class SkipGetRepositoryOnNormalRepository
{
    public function run(EntityManagerInterface $entityManager)
    {
        $someRepository = $entityManager->getRepository(NormalEntity::class);
    }
}
