<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RouteGenerateControllerClassRequireNameRule\Fixture;

use Symfony\Component\Routing\RouterInterface;
use Symplify\PHPStanRules\Tests\Rules\Symfony\RouteGenerateControllerClassRequireNameRule\Source\SomeControllerWithStringNameClass;

final class CallingControllerWithWrongString
{
    public function run(RouterInterface $router)
    {
        $url = $router->generate(SomeControllerWithStringNameClass::class);
    }
}
