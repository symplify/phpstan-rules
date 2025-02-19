<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule\Source;

use Symfony\Component\Routing\Attribute\Route;

class SomeControllerWithRouteClass
{
    #[Route(name: self::class)]
    public function __invoke()
    {
    }
}
