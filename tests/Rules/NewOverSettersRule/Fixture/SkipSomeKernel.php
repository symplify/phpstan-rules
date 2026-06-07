<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Source\SomeKernel;
use Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Source\SomeObject;

final class SkipSomeKernel
{
    public function first()
    {
        $someKernel = new SomeKernel();
        $someKernel->setName('John');
    }

    public function second()
    {
        $someKernel = new SomeKernel();
        $someKernel->setName('John');
    }
}
