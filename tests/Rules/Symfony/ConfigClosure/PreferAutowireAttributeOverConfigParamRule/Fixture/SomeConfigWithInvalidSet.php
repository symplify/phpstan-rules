<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\ServicesExcludedDirectoryMustExistRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\PreferAutowireAttributeOverConfigParamRule\Source\SomeSetServiceWithConstructor;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(SomeSetServiceWithConstructor::class)
        ->arg('$key', param('parameter_name'));
};
