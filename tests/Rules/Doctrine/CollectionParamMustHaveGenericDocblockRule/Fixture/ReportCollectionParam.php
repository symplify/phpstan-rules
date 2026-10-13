<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionParamMustHaveGenericDocblockRule\Fixture;

use Doctrine\Common\Collections\Collection;

final class ReportCollectionParam
{
    public function setItems(Collection $items): void
    {
    }
}
