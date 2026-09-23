<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Source\RandomEntity;

/**
 * @extends EntityRepository<RandomEntity>
 */
final class SkipDeleteBuilder extends EntityRepository
{
    public function assigned(EntityManagerInterface $entityManager): void
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->delete(RandomEntity::class, 'r')
            ->where('r.id = :id');
    }
}
