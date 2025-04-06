<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoDuplicateArgAutowireByTypeRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoDuplicateArgAutowireByTypeRule\Source\AnotherType;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoDuplicateArgAutowireByTypeRule\Source\SomeServiceWithConstructor;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(SomeServiceWithConstructor::class)
        ->args([service(AnotherType::class)]);
};
