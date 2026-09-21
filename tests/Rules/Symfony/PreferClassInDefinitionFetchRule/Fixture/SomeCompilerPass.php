<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\PreferClassInDefinitionFetchRule\Fixture;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PHPStanRules\Tests\Rules\Symfony\PreferClassInDefinitionFetchRule\Source\SomeHelper;

final class SomeCompilerPass
{
    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition(SomeHelper::class);

        $container->getDefinition('Symplify\PHPStanRules\Tests\Rules\Symfony\PreferClassInDefinitionFetchRule\Source\SomeHelper');

        $container->getDefinition('some.service.id');
    }
}
