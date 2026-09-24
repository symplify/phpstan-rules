<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoFindTaggedServiceIdsCallRule\Fixture;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FindTaggedServiceIdsSimpleForeach implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $definition = $containerBuilder->getDefinition('some_service');

        $taggedServices = $containerBuilder->findTaggedServiceIds('some_tag');
        foreach ($taggedServices as $id => $tag) {
            $definition->addMethodCall('addTransport', [$tag]);
        }
    }
}
