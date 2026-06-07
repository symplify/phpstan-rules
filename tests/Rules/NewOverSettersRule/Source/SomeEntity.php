<?php

namespace Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Source;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SomeEntity
{
    public function setName(string $name)
    {
    }

    public function setAge(int $age)
    {
    }
}
