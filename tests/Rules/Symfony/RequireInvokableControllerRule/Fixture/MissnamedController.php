<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireInvokableControllerRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class MissnamedController extends AbstractController
{
    /**
     * @Route()
     */
    public function run()
    {
    }
}
