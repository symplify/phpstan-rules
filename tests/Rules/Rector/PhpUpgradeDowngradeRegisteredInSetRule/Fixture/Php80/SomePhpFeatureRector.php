<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\PhpUpgradeDowngradeRegisteredInSetRule\Fixture\Php80;

use PhpParser\Node;
use Rector\Rector\AbstractRector;

final class SomePhpFeatureRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
    }

    public function refactor(Node $node)
    {
    }
}
