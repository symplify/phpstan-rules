<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

final class InterfaceCollector implements Collector
{
    public function getNodeType(): string
    {
        return Interface_::class;
    }

    /**
     * @param Interface_ $node
     */
    public function processNode(Node $node, Scope $scope): ?string
    {
        if (! $node->namespacedName instanceof Name) {
            return null;
        }

        return $node->namespacedName->toString();
    }
}
