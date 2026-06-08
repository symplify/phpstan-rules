<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\PHPStanRules\Tests\ReturnTypeExtension\ContainerGetReturnTypeExtension\Source\ExternalService;
use function PHPStan\Testing\assertType;

final class SomeClass
{
    public function run(ContainerInterface $container): void
    {
        $service = $container->get(ExternalService::class);
        assertType(ExternalService::class, $service);
    }
}
