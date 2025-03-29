<?php

namespace Symplify\PHPStanRules\Tests\Rules\StringFileAbsolutePathExistsRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->load('App\\', __DIR__ . '/../src/*Repository.php');
};
