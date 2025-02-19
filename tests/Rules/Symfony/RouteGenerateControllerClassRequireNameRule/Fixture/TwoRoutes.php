<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RouteGenerateControllerClassRequireNameRule\Fixture;

use Symfony\Component\Routing\RouterInterface;
use Symplify\PHPStanRules\Tests\Rules\Symfony\RouteGenerateControllerClassRequireNameRule\Source\TwoRoutesController;

final class TwoRoutes
{
    public function run(RouterInterface $router)
    {
        $url = $router->generate(TwoRoutesController::class);
    }
}
