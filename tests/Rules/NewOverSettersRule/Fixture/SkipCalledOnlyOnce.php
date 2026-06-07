<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Source\SomeObject;

final class SkipCalledOnlyOnce
{
    public function first()
    {
        $alwaysSetters = new SomeObject();
        $alwaysSetters->setName('John');
        $alwaysSetters->setAge(55);
    }
}
