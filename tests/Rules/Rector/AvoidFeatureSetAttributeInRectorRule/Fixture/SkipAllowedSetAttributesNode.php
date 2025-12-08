<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\AvoidFeatureSetAttributeInRectorRule\Fixture;

use PhpParser\Node;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use ReflectionClass;

final class SkipAllowedSetAttributesNode extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Stmt\Class_::class];
    }

    public function refactor(Node $node)
    {
        $node->setAttribute(AttributeKey::ORIGINAL_NODE, null);
        $node->setAttribute(AttributeKey::KIND, 1);

        return null;
    }
}
