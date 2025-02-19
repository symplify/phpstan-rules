<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RouteGenerateControllerClassRequireNameRule\Fixture;

use Symfony\Component\Routing\RouterInterface;
use Symplify\PHPStanRules\Tests\Rules\Symfony\RouteGenerateControllerClassRequireNameRule\Source\SomeControllerWIthoutRouteClass;

final class CallingWrongController
{
    public function run(RouterInterface $router)
    {
        $url = $router->generate(SomeControllerWIthoutRouteClass::class);
    }
}
