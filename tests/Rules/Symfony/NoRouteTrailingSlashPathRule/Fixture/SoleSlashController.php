<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoRouteTrailingSlashPathRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class SoleSlashController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function someAction()
    {
    }
}
