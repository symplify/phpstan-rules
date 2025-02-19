<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RouteGenerateControllerClassRequireNameRule\Fixture;

use Symfony\Component\Routing\RouterInterface;
use Symplify\PHPStanRules\Tests\Rules\Symfony\RouteGenerateControllerClassRequireNameRule\Source\SomeControllerWithRouteClass;

final class CallingCorrectController
{
    public function run(RouterInterface $router)
    {
        $url = $router->generate(SomeControllerWithRouteClass::class);
    }
}
