<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoReadonlyEntityClassRule\Fixture;

use Doctrine\ORM\Mapping\Entity;

#[Entity]
final readonly class ReportReadonlyAttributeEntity
{
}
