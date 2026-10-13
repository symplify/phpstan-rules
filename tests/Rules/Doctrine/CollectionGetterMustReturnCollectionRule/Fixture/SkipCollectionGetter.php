<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\CollectionGetterMustReturnCollectionRule\Fixture;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ManyToMany;

final class SkipCollectionGetter
{
    #[ManyToMany]
    private $tags;

    private $name;

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getName()
    {
        return $this->name;
    }
}
