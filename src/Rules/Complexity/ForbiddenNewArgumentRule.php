<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<New_>
 */
final class ForbiddenNewArgumentRule implements Rule
{
    /**
     * @var string[]
     * @readonly
     */
    private array $forbiddenTypes;
    /**
     * @param string[] $forbiddenTypes
     */
    public function __construct(array $forbiddenTypes)
    {
        $this->forbiddenTypes = $forbiddenTypes;
    }

    public function getNodeType(): string
    {
        return New_::class;
    }

    /**
     * @param New_ $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->class instanceof Name) {
            return [];
        }

        $className = $node->class->toString();
        if (! in_array($className, $this->forbiddenTypes)) {
            return [];
        }

        $errorMessage = sprintf(
            'Type "%s" is forbidden to be created manually. Use service and constructor injection instead',
            $className
        );

        $identifierRuleError = RuleErrorBuilder::message($errorMessage)
            ->identifier(RuleIdentifier::FORBIDDEN_NEW_INSTANCE)
            ->build();

        return [$identifierRuleError];
    }
}
