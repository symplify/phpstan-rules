<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\PreferClassServiceReferenceRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\PreferClassServiceReferenceRule\Source\SomeHelper;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(SomeHelper::class)
        ->args([service('unaliased.service')]);
};
