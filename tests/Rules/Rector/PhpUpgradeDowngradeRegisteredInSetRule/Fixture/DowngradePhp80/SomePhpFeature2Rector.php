<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\PhpUpgradeDowngradeRegisteredInSetRule\Fixture\DowngradePhp80;

use PhpParser\Node;
use Rector\Contract\Rector\RectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class SomePhpFeature2Rector extends AbstractRector implements RectorInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
    }

    public function getNodeTypes(): array
    {
        return [];
    }

    public function refactor(Node $node)
    {
        return null;
    }
}
