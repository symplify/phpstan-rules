<?php

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Source\Repository;

use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepositoryInterface;

final class RepositoryWithInterface implements ServiceDocumentRepositoryInterface
{
}
