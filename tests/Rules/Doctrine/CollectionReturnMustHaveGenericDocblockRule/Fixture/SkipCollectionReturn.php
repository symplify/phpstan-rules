<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionReturnMustHaveGenericDocblockRule\Fixture;

use Doctrine\Common\Collections\Collection;

final class SkipCollectionReturn
{
    /**
     * @return Collection<int, string>
     */
    public function getItems(): Collection
    {
    }

    public function getName(): string
    {
        return '';
    }
}
