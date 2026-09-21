<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\PreferInterfaceInConstructorRule\Fixture;

use Symfony\Component\Routing\RouterInterface;

final class SkipRouterInterface
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }
}
