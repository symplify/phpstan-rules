<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule\Fixture;

use Symfony\Component\Routing\RouterInterface;
use Symplify\PHPStanRules\Tests\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule\Source\SomeControllerWithStringNameClass;

final class CallingControllerWithWrongString
{
    public function run(RouterInterface $router)
    {
        $url = $router->generate(SomeControllerWithStringNameClass::class);
    }
}
