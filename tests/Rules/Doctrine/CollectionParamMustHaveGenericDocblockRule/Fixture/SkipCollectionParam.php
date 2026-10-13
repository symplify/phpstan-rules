<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionParamMustHaveGenericDocblockRule\Fixture;

use Doctrine\Common\Collections\Collection;

final class SkipCollectionParam
{
    /**
     * @param Collection<int, string> $items
     */
    public function setItems(Collection $items): void
    {
    }

    public function setName(string $name): void
    {
    }
}
