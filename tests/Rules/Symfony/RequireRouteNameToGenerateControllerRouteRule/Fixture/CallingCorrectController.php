<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule\Fixture;

use Symfony\Component\Routing\RouterInterface;
use Symplify\PHPStanRules\Tests\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule\Source\SomeControllerWithRouteClass;

final class CallingCorrectController
{
    public function run(RouterInterface $router)
    {
        $url = $router->generate(SomeControllerWithRouteClass::class);
    }
}
