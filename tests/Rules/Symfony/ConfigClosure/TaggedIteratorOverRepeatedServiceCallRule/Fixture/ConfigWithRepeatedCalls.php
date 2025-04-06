<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\ServicesExcludedDirectoryMustExistRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('some')
        ->call('repeatedMethod', [service('some')])
        ->call('repeatedMethod', [service('some')])
        ->call('repeatedMethod', [service('some')])
        ->call('repeatedMethod', [service('some')])
        ->call('repeatedMethod', [service('some')])
        ->call('repeatedMethod', [service('some')])
        ->call('repeatedMethod', [service('some')]);
};
