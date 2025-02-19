<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule\Fixture;

use Symfony\Component\Routing\RouterInterface;
use Symplify\PHPStanRules\Tests\Rules\Symfony\RequireRouteNameToGenerateControllerRouteRule\Source\TwoRoutesController;

final class TwoRoutes
{
    public function run(RouterInterface $router)
    {
        $url = $router->generate(TwoRoutesController::class);
    }
}
