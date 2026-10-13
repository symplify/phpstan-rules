<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionPropertyMustHaveGenericDocblockRule\Fixture;

use Doctrine\Common\Collections\Collection;

final class ReportCollectionProperty
{
    private Collection $items;
}
