<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RouteGenerateControllerClassRequireNameRule\Source;

use Symfony\Component\Routing\Attribute\Route;

class WithoutInvokeController
{
    #[Route()]
    public function run()
    {
    }
}
