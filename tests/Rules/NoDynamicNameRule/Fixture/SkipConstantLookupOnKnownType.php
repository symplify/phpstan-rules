<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Source\SomeClassConstantA;
use Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Source\SomeClassConstantB;

final class SkipConstantLookupOnKnownType
{
    public function run()
    {
        $objectOfKnownType = $this->getObject();
        if ($objectOfKnownType::MY_CONSTANT === 3) {
            echo "a";
        }
    }

    private function getObject():SomeClassConstantA|SomeClassConstantB {
        return rand(0,1) ? new SomeClassConstantA() : new SomeClassConstantB();
    }
}
