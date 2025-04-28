<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Fixture;

use Doctrine\DBAL\Connection;

final class SkipConnection
{
    public function process(Connection $connection)
    {
        $someQueryBuilder = $connection->createQueryBuilder();
    }
}
