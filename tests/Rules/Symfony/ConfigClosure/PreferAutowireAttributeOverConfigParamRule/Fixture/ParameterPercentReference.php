<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\ServicesExcludedDirectoryMustExistRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\PreferAutowireAttributeOverConfigParamRule\Source\SomeSetServiceWithConstructor;

return function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(SomeSetServiceWithConstructor::class)
        ->arg('$key', '%parameter_name%');
};
