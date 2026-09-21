<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\PreferInterfaceInConstructorRule\Fixture;

use Symfony\Component\Routing\Router;

final class ReportConcreteRouter
{
    public function __construct(
        private Router $router,
    ) {
    }
}
