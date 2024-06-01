<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

final class ImplementedInterfaceCollector implements Collector
{
    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $implementedInterfaceNames = [];

        foreach ($node->implements as $implement) {
            $implementedInterfaceNames[] = $implement->toString();
        }

        return $implementedInterfaceNames;
    }
}
