<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoControllerMethodInjectionRule\Fixture;

use Symfony\Component\Routing\Annotation\Route;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoControllerMethodInjectionRule\Source\SomeService;

final class InvokableActionInjectionController
{
    /**
     * @Route("/some-action", name="some_action")
     */
    public function __invoke(SomeService $someService)
    {
    }
}
