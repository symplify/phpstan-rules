<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoStringTargetEntityRule\Fixture;

use Doctrine\ORM\Mapping\ManyToOne;

final class ReportStringTargetEntity
{
    #[ManyToOne(targetEntity: 'App\Entity\Category')]
    private $category;
}
