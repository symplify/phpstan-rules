<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoStringTargetEntityRule\Fixture;

use Doctrine\ORM\Mapping\ManyToOne;

final class SkipClassConstTargetEntity
{
    #[ManyToOne(targetEntity: Category::class)]
    private $category;
}
