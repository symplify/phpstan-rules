<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\PhpUpgradeDowngradeRegisteredInSetRule\Fixture\DowngradePhp80;

use PhpParser\Node;
use Rector\Rector\AbstractRector;

final class SomePhpFeature2Rector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [];
    }

    public function refactor(Node $node)
    {
        return null;
    }
}
