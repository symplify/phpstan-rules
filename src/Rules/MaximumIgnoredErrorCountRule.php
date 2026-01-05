<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\DependencyInjection\NeonAdapter;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<CollectedDataNode>
 */
final class MaximumIgnoredErrorCountRule implements Rule
{
    /**
     * @readonly
     */
    private int $limit = 0;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = "Ignored error count %d in phpstan.neon surpassed maximum limit %d.\nInstead of ignoring more errors, fix them to keep your codebase fit.";

    /**
     * @readonly
     */
    private NeonAdapter $neonAdapter;

    public function __construct(
        int $limit = 0
    ) {
        $this->limit = $limit;
        $this->neonAdapter = new NeonAdapter([]);
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        // hack to run this rule just once
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // not enabled yet, use "
        if ($this->limit === 0) {
            return [];
        }

        $configFilePath = getcwd() . '/phpstan.neon';

        // unable to find config
        if (! file_exists($configFilePath)) {
            return [];
        }

        $phpstanNeon = $this->neonAdapter->load($configFilePath);
        $ignoreErrors = $phpstanNeon['parameters']['ignoreErrors'] ?? [];
        if (count($ignoreErrors) <= $this->limit) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, count($ignoreErrors), $this->limit);

        $identifierRuleError = RuleErrorBuilder::message($errorMessage)
            ->identifier(RuleIdentifier::MAXIMUM_IGNORED_ERROR_COUNT)
            ->nonIgnorable()
            ->build();

        return [$identifierRuleError];
    }
}
