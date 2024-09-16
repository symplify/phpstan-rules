<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

final class InterfaceOfAbstractClassCollector implements Collector
{
    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return non-empty-array<string>|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (! $node->isAbstract()) {
            return null;
        }

        $interfaceNames = [];

        foreach ($node->implements as $implement) {
            $interfaceNames[] = $implement->toString();
        }

        if ($interfaceNames === []) {
            return null;
        }

        return $interfaceNames;
    }
}
