<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Source\RandomEntity;

/**
 * @extends EntityRepository<RandomEntity>
 */
final class ReportOnSelfEntityFrom extends EntityRepository
{
    public function fluent(EntityManagerInterface $entityManager): void
    {
        $entityManager->createQueryBuilder()
            ->select('r')
            ->from(RandomEntity::class, 'r');
    }

    public function assigned(EntityManagerInterface $entityManager): void
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('r')
            ->from(RandomEntity::class, 'r');
    }
}
