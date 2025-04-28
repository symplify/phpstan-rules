<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Source\RandomEntity;

final class ReportOnEntityManager
{
    public function process(EntityManagerInterface $entityManager)
    {
        $someRepository = $entityManager->createQueryBuilder();
    }
}
