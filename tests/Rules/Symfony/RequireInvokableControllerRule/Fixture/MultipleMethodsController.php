<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireInvokableControllerRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class MultipleMethodsController extends AbstractController
{
    /**
     * @\Symfony\Component\Routing\Annotation\Route()
     */
    public function run()
    {
    }

    /**
     * @\Symfony\Component\Routing\Annotation\Route()
     */
    public function go()
    {
    }
}
