<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireServiceRepositoryParentRuleTest\Fixture;

use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepositoryInterface;

final class SkipContractImplementingRepository implements ServiceDocumentRepositoryInterface
{
}
