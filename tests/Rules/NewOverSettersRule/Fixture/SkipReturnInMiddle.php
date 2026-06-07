<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Source\SomeObject;

final class SkipReturnInMiddle
{
    public function first()
    {
        $alwaysSetters = new SomeObject();
        $alwaysSetters->setName('John');

        if (mt_rand(0, 100) > 50) {
            return;
        }

        $alwaysSetters->setAge(25);
    }

    public function second()
    {
        $alwaysSetters = new SomeObject();
        $alwaysSetters->setName('Doe');
        $alwaysSetters->setAge(35);
    }
}
