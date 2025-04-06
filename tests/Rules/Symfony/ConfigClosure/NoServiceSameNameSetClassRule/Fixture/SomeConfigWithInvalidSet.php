<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\ServicesExcludedDirectoryMustExistRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSameNameSetClassRule\Source\SomeSetService;

return function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(SomeSetService::class, SomeSetService::class);
};
