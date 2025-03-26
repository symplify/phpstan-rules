<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\ServicesExcludedDirectoryMustExistRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\AlreadyRegisteredAutodiscoveryServiceRule\Source\RegisterAsService;

return function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->load('Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\\', __DIR__ . '/../src');

    $services->set(RegisterAsService::class);
};
