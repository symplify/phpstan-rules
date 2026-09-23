<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Source\SomeAttributeEntity;

final class SkipAttributeEntity
{
    public function first()
    {
        $alwaysSetters = new SomeAttributeEntity();
        $alwaysSetters->setName('John');
        $alwaysSetters->setAge(25);
    }

    public function second()
    {
        $alwaysSetters = new SomeAttributeEntity();
        $alwaysSetters->setName('Doe');
        $alwaysSetters->setAge(35);
    }
}
