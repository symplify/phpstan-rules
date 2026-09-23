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
final class SkipCrossEntityFrom extends EntityRepository
{
    public function fluent(EntityManagerInterface $entityManager): void
    {
        $entityManager->createQueryBuilder()
            ->select('a')
            ->from(AnotherEntity::class, 'a');
    }

    public function assigned(EntityManagerInterface $entityManager): void
    {
        $subQueryBuilder = $entityManager->createQueryBuilder();
        $subQueryBuilder->select('a')
            ->from(AnotherEntity::class, 'a');
    }
}
