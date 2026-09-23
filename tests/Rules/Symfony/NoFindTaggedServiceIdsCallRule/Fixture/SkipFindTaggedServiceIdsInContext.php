<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoFindTaggedServiceIdsCallRule\Fixture;

use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SkipFindTaggedServiceIdsInContext
{
    public function test(ContainerBuilder $containerBuilder): void
    {
        $taggedServiceIds = $containerBuilder->findTaggedServiceIds('some_tag');
    }
}
