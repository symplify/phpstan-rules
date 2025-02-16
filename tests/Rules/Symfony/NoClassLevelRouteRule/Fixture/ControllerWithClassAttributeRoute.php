<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoClassLevelRouteRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/global-path')]
class ControllerWithClassAttributeRoute extends AbstractController
{

}
