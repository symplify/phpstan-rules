<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\TraitDefinitionCollector;
use Symplify\PHPStanRules\Collector\TraitUsageCollector;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @see TraitDefinitionCollector
 * @see TraitUsageCollector
 * @see \Symplify\PHPStanRules\Tests\Rules\PreferInlineTraitRule\PreferInlineTraitRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class PreferInlineTraitRule implements Rule
{
    /**
     * @readonly
     */
    private int $maxUsage;

    /**
     * @readonly
     */
    private bool $isEnabled;

    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Trait "%s" (%d lines) is used only %d-time(s).%sInline it into its user(s) to empower IDE, Rector and PHPStan';

    public function __construct(int $maxUsage, bool $isEnabled)
    {
        $this->maxUsage = $maxUsage;
        $this->isEnabled = $isEnabled;
    }

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // enable with "inlineTrait: true" parameter
        if (! $this->isEnabled) {
            return [];
        }

        $usageCountByTraitName = $this->countUsagesByTraitName($node);

        $ruleErrors = [];

        foreach ($node->get(TraitDefinitionCollector::class) as $traitDefinitions) {
            foreach ($traitDefinitions as $traitDefinition) {
                $traitName = $traitDefinition['traitName'];
                $usageCount = $usageCountByTraitName[$traitName] ?? 0;

                if ($usageCount > $this->maxUsage) {
                    continue;
                }

                $errorMessage = sprintf(
                    self::ERROR_MESSAGE,
                    $traitName,
                    $traitDefinition['lineCount'],
                    $usageCount,
                    PHP_EOL
                );

                $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                    ->identifier(RuleIdentifier::PREFER_INLINE_TRAIT)
                    ->file($traitDefinition['file'])
                    ->line($traitDefinition['line'])
                    ->build();
            }
        }

        return $ruleErrors;
    }

    /**
     * @return array<string, int>
     */
    private function countUsagesByTraitName(CollectedDataNode $collectedDataNode): array
    {
        $usageCountByTraitName = [];

        foreach ($collectedDataNode->get(TraitUsageCollector::class) as $traitUsagesInFiles) {
            foreach ($traitUsagesInFiles as $traitUsageInFile) {
                foreach ($traitUsageInFile as $traitName) {
                    $usageCountByTraitName[$traitName] = ($usageCountByTraitName[$traitName] ?? 0) + 1;
                }
            }
        }

        return $usageCountByTraitName;
    }
}
