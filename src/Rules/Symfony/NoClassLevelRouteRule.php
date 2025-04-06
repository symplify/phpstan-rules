<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyControllerAnalyzer;

/**
 * @implements Rule<InClassNode>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoClassLevelRouteRule\NoClassLevelRouteRuleTest
 */
final class NoClassLevelRouteRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid class-level route prefixing. Use method route to keep single source of truth and focus';

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! SymfonyControllerAnalyzer::isControllerScope($scope)) {
            return [];
        }

        $classLike = $node->getOriginalNode();
        if (! SymfonyControllerAnalyzer::hasRouteAnnotationOrAttribute($classLike)) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->line($node->getStartLine())
            ->identifier(SymfonyRuleIdentifier::NO_CLASS_LEVEL_ROUTE)
            ->build();

        return [$ruleError];
    }
}
