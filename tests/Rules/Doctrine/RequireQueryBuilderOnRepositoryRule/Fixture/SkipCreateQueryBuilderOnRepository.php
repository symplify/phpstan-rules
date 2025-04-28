<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Source\RandomEntity;

final class SkipCreateQueryBuilderOnRepository
{
    public function process(EntityManagerInterface $entityManager)
    {
        $someRepository = $entityManager->getRepository(RandomEntity::class);

        $queryBuilder = $someRepository
            ->createQueryBuilder();
    }

    public function directlyOnCall(EntityManagerInterface $entityManager)
    {
        $queryBuilder = $entityManager->getRepository(RandomEntity::class)
            ->createQueryBuilder();
    }
}
