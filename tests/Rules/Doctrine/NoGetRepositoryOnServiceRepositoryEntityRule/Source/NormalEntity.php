<?php

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Source;

use Doctrine\ORM\Mapping as ORM;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoGetRepositoryOnServiceRepositoryEntityRule\Source\NormalRepository;

/**
 * @ORM\Entity(repositoryClass=NormalRepository::class)
 */
class NormalEntity
{
}
