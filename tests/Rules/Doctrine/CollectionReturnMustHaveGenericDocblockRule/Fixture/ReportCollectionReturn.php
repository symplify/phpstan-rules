<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionReturnMustHaveGenericDocblockRule\Fixture;

use Doctrine\Common\Collections\Collection;

final class ReportCollectionReturn
{
    public function getItems(): Collection
    {
    }
}
