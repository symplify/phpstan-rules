<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequiredOnlyInAbstractRule\Fixture;

final class SkipParentDocumentRepository extends \Doctrine\ODM\MongoDB\Repository\DocumentRepository
{
    /**
     * @required
     */
    public function autowire()
    {
    }
}
