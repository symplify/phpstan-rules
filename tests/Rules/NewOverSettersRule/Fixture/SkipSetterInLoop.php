<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Source\SomeObject;

final class SkipSetterInLoop
{
    public function first(array $items)
    {
        $alwaysSetters = new SomeObject();
        foreach (['John', 'Doe'] as $name) {
            $alwaysSetters->setName($name);
            foreach ($items as $item) {
                echo $item;
            }
        }
    }

    public function second(array $items)
    {
        $alwaysSetters = new SomeObject();
        foreach (['Jane', 'Roe'] as $name) {
            $alwaysSetters->setName($name);
            foreach ($items as $item) {
                echo $item;
            }
        }
    }
}
