<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;

trait SkipCreateQueryBuilderInTrait
{
    public function process(EntityManagerInterface $entityManager)
    {
        $someRepository = $entityManager->createQueryBuilder();
    }
}

final class SkipRepositoryUsingTrait extends \Doctrine\ORM\EntityRepository
{
    use SkipCreateQueryBuilderInTrait;
}
