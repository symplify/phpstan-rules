<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoFindTaggedServiceIdsCallRule\Fixture;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class SkipTagAttributesInForeach implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $definition = $containerBuilder->getDefinition('some_service');

        $taggedServices = $containerBuilder->findTaggedServiceIds('some_tag');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addTransport', [
                $id,
                new Reference($id),
                ! empty($tags[0]['alias']) ? $tags[0]['alias'] : $id,
                ! empty($tags[0]['integrationAlias']) ? $tags[0]['integrationAlias'] : $id,
            ]);
        }
    }
}
