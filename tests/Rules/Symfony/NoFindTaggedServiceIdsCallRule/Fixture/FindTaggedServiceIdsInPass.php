<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoFindTaggedServiceIdsCallRule\Fixture;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FindTaggedServiceIdsInPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $taggedServiceIds = $containerBuilder->findTaggedServiceIds('some_tag');
    }
}
