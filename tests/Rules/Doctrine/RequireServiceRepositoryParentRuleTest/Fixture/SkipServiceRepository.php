<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireServiceRepositoryParentRuleTest\Fixture;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

final class SkipServiceRepository extends ServiceEntityRepository
{
}
