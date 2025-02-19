<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RouteGenerateControllerClassRequireNameRule\Source;

use Symfony\Component\Routing\Attribute\Route;

class SomeControllerWIthoutRouteClass
{
    #[Route()]
    public function __invoke()
    {
    }
}
