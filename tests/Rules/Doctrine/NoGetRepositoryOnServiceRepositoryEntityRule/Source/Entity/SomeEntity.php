<?php

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Source\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=\Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Source\Repository\SomeServiceRepository::class)
 */
class SomeEntity
{
}
