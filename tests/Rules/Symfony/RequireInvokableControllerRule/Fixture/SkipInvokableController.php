<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireInvokableControllerRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SkipInvokableController extends AbstractController
{
    /**
     * @Route()
     */
    public function __invoke()
    {
    }
}
