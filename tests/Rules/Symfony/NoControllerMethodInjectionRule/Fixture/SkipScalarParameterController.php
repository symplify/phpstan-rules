<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoControllerMethodInjectionRule\Fixture;

use Symfony\Component\Routing\Annotation\Route;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoControllerMethodInjectionRule\Source\SomeService;

final class SkipScalarParameterController
{
    /**
     * @Route("/some-action", name="some_action")
     */
    public function someRequired(int $id)
    {
    }
}
