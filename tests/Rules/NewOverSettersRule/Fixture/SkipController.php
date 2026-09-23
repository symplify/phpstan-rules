<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NewOverSettersRule\Source\SomeController;

final class SkipController
{
    public function first()
    {
        $someController = new SomeController();
        $someController->setContainer('some_container');
    }

    public function second()
    {
        $someController = new SomeController();
        $someController->setContainer('some_container');
    }
}
