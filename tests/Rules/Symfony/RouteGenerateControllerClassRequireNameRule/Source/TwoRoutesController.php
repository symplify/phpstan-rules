<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RouteGenerateControllerClassRequireNameRule\Source;

use Symfony\Component\Routing\Attribute\Route;

final class TwoRoutesController
{
    #[Route(name: self::class)]
    #[Route(name: 'another')]
    public function __invoke()
    {
    }
}
