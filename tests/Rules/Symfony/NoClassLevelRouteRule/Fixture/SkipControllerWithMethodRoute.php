<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoClassLevelRouteRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class SkipControllerWithMethodRoute extends AbstractController
{
    #[Route('/global-path')]
    public function someMethod()
    {

    }
}
