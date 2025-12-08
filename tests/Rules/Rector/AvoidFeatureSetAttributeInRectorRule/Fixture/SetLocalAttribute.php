<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\AvoidFeatureSetAttributeInRectorRule\Fixture;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use ReflectionClass;

final class SetLocalAttribute extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Stmt\Class_::class];
    }

    public function refactor(Node $node)
    {
        $node->setAttribute('some_attribute', 'some_value');

        return null;
    }
}
