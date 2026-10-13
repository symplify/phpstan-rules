<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionGetterMustReturnCollectionRule\Fixture;

use Doctrine\ORM\Mapping\OneToMany;

final class ReportCollectionGetter
{
    #[OneToMany]
    private $items;

    public function getItems()
    {
        return $this->items;
    }
}
