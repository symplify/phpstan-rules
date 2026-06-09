<?php

declare(strict_types=1);

use Illuminate\Container\Container;
use Symplify\PHPStanRules\Tests\ReturnTypeExtension\LaravelContainerMakeTypeExtension\Source\ExternalService;
use function PHPStan\Testing\assertType;

final class SomeClass
{
    public function run(Container $container): void
    {
        $service = $container->make(ExternalService::class);
        assertType(ExternalService::class, $service);

        $service = $container->get(ExternalService::class);
        assertType(ExternalService::class, $service);
    }
}
