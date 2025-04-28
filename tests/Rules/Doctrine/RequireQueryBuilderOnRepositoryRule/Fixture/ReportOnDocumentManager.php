<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RequireQueryBuilderOnRepositoryRule\Fixture;

use Doctrine\ODM\MongoDB\DocumentManager;

final class ReportOnDocumentManager
{
    public function process(DocumentManager $documentManager)
    {
        $someRepository = $documentManager->createQueryBuilder();
    }
}
