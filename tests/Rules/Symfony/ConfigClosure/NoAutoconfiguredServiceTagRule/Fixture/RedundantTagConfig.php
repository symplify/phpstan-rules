<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoAutoconfiguredServiceTagRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoAutoconfiguredServiceTagRule\Source\SomeSubscriber;

return function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->autoconfigure();

    $services->set(SomeSubscriber::class)
        ->tag('kernel.event_subscriber');
};
