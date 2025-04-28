<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireServiceRepositoryParentRule\Fixture;

use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepositoryInterface;

final class SkipContractImplementingRepository implements ServiceDocumentRepositoryInterface
{
}
