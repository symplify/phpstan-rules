<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector;

use PhpParser\Node;
use PhpParser\Node\Stmt\TraitUse;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * Collect every "use SomeTrait;" occurrence as a trait FQN,
 * so the rule can count how many times each trait is used across the project.
 *
 * @implements Collector<TraitUse, string[]>
 */
final class TraitUsageCollector implements Collector
{
    /**
     * @readonly
     */
    private bool $isEnabled;

    public function __construct(bool $isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    public function getNodeType(): string
    {
        return TraitUse::class;
    }

    /**
     * @param TraitUse $node
     * @return string[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        // enable with "inlineTrait: true" parameter
        if (! $this->isEnabled) {
            return null;
        }

        $traitNames = [];
        foreach ($node->traits as $traitName) {
            $traitNames[] = $traitName->toString();
        }

        return $traitNames;
    }
}
