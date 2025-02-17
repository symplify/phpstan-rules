<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoRouteTrailingSlashPathRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class InvalidWithNameController extends AbstractController
{
    /**
     * @Route("/next-route/", name="more-text")
     */
    public function someAction()
    {
    }
}
