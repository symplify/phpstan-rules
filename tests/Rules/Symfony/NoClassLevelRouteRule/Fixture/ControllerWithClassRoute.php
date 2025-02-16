<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoClassLevelRouteRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/global-path")
 */
class ControllerWithClassRoute extends AbstractController
{
}
