<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Source\AnotherEntity;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Source\RandomEntity;

/**
 * @extends EntityRepository<RandomEntity>
 */
final class MixedBuildersInOneMethod extends EntityRepository
{
    public function run(EntityManagerInterface $entityManager): void
    {
        $mainQueryBuilder = $entityManager->createQueryBuilder();
        $mainQueryBuilder->select('r')
            ->from(RandomEntity::class, 'r');

        $subQueryBuilder = $entityManager->createQueryBuilder();
        $subQueryBuilder->select('count(a.id)')
            ->from(AnotherEntity::class, 'a');
    }
}
