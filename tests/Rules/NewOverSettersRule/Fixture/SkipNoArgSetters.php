<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Source\SomeTimerObject;

final class SkipNoArgSetters
{
    public function first()
    {
        $someTimerObject = new SomeTimerObject();
        $someTimerObject->setStartTime();
        $someTimerObject->setEndTime();
    }

    public function second()
    {
        $someTimerObject = new SomeTimerObject();
        $someTimerObject->setStartTime();
        $someTimerObject->setEndTime();
    }
}
