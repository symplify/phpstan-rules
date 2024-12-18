<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoParentRepositoryRule\Fixture;

use Doctrine\ORM\EntityRepository;

final class SomeRepository extends EntityRepository
{
}
