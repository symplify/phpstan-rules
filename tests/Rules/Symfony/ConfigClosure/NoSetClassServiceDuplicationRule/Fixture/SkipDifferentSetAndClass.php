<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\ServicesExcludedDirectoryMustExistRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoSetClassServiceDuplicationRule\Source\SomeClassToBeSet;

return function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(SomeClassToBeSet::class)
        ->class('AnotherValue');
};
