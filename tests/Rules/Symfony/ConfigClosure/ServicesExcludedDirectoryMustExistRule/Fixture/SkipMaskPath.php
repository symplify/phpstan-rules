<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\ServicesExcludedDirectoryMustExistRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->load('App\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../{missing,here}'
        ]);
};
