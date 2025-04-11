<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSameNameSetClassRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSameNameSetClassRule\Source\SomeSetService;

class SkipNonClosureConstantSet
{
    private const NAME = 'name';

    private const TYPE = 'type';

    public function run()
    {
        $this->set(self::NAME, self::TYPE);
    }
}
