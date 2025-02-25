<?php

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Source;

use Doctrine\ORM\Mapping as ORM;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Source\SomeServiceRepository;

/**
 * @ORM\Entity(repositoryClass=SomeServiceRepository::class)
 */
class SomeEntity
{
}
