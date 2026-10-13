<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionPropertyMustHaveGenericDocblockRule\Fixture;

use Doctrine\Common\Collections\Collection;

final class SkipCollectionProperty
{
    /**
     * @var Collection<int, string>
     */
    private Collection $items;

    private string $name;
}
