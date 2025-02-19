<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule\Source;

use Symfony\Component\Routing\Attribute\Route;

class SomeControllerWIthoutRouteClass
{
    #[Route()]
    public function __invoke()
    {
    }
}
