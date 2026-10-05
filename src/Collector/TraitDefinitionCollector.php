<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * Collect every trait definition with its FQN, file, line and line count,
 * so the rule can decide which traits are used too rarely to justify existence.
 *
 * @implements Collector<Trait_, array{traitName: string, file: string, line: int, lineCount: int}>
 */
final class TraitDefinitionCollector implements Collector
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
        return Trait_::class;
    }

    /**
     * @param Trait_ $node
     * @return array{traitName: string, file: string, line: int, lineCount: int}|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        // enable with "inlineTrait: true" parameter
        if (! $this->isEnabled) {
            return null;
        }

        $traitName = (($nullsafeVariable1 = $node->namespacedName) ? $nullsafeVariable1->toString() : null) ?? (($nullsafeVariable2 = $node->name) ? $nullsafeVariable2->toString() : null);
        if ($traitName === null) {
            return null;
        }

        return [
            'traitName' => $traitName,
            'file' => $scope->getFile(),
            'line' => $node->getStartLine(),
            'lineCount' => $node->getEndLine() - $node->getStartLine() + 1,
        ];
    }
}
