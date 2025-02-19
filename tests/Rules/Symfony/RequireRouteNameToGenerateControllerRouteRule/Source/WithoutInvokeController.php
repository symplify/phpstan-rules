<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule\Source;

use Symfony\Component\Routing\Attribute\Route;

class WithoutInvokeController
{
    #[Route()]
    public function run()
    {
    }
}
