<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\PreferInterfaceInConstructorRule\Fixture;

final class SkipProjectClass
{
    public function __construct(
        private SkipRouterInterface $service,
    ) {
    }
}
