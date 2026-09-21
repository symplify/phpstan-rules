<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSetterCallRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSetterCallRule\Source\SomeConsumer;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSetterCallRule\Source\SomeService;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(SomeConsumer::class)
        ->call('setSomeService', [service(SomeService::class)]);
};
