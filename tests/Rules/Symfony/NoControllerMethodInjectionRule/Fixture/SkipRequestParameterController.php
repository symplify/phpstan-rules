<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoControllerMethodInjectionRule\Fixture;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class SkipRequestParameterController
{
    /**
     * @Route("/some-action", name="some_action")
     */
    public function someRequired(Request $request)
    {
    }
}
