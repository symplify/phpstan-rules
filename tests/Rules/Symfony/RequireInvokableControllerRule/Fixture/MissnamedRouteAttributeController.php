<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireInvokableControllerRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class MissnamedRouteAttributeController extends AbstractController
{
     #[Route]
    public function run()
    {
    }
}
