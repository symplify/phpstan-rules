<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Fixture;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

final class ReportOnDocumentManager extends DocumentRepository
{
    public function process(DocumentManager $documentManager)
    {
        $someRepository = $documentManager->createQueryBuilder();
    }
}
