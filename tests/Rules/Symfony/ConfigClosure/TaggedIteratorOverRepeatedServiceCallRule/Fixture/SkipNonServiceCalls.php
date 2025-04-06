<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\ServicesExcludedDirectoryMustExistRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('some')
        ->call('repeatedMethod', [100])
        ->call('repeatedMethod', [100])
        ->call('repeatedMethod', [100])
        ->call('repeatedMethod', [100])
        ->call('repeatedMethod', [100])
        ->call('repeatedMethod', [100])
        ->call('repeatedMethod', [100]);
};
