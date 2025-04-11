<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSameNameSetClassRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSameNameSetClassRule\Source\ConstantList;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSameNameSetClassRule\Source\SomeSetService;

return function (ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $parameters->set(ConstantList::NAME, ConstantList::NAME);
};
