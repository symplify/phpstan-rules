<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Fixture;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Source\RandomEntity;

final class SkipDocumentRepository
{
    public function process(DocumentManager $documentManager)
    {
        $someRepository = $documentManager->getRepository(RandomEntity::class)
            ->createQueryBuilder();
    }
}
