<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;

final class SkipNonRepositoryClass
{
    public function process(EntityManagerInterface $entityManager)
    {
        $queryBuilder = $entityManager->createQueryBuilder();
    }
}
