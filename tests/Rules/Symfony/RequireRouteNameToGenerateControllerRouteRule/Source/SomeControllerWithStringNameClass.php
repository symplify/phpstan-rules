<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule\Source;

use Symfony\Component\Routing\Attribute\Route;

class SomeControllerWithStringNameClass
{
    #[Route(name: 'something_else')]
    public function __invoke()
    {
    }
}
