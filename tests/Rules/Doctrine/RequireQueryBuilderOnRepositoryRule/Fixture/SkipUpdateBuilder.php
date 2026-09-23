<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Source\RandomEntity;

/**
 * @extends EntityRepository<RandomEntity>
 */
final class SkipUpdateBuilder extends EntityRepository
{
    public function fluent(EntityManagerInterface $entityManager): void
    {
        $entityManager->createQueryBuilder()
            ->update(RandomEntity::class, 'r')
            ->where('r.active = :active');
    }

    public function assigned(EntityManagerInterface $entityManager): void
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->update(RandomEntity::class, 'r');
    }
}
