<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\MethodName;
use Symplify\PHPStanRules\Enum\SymfonyClass;
use Symplify\PHPStanRules\Enum\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyControllerAnalyzer;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\RequireInvokableControllerRule\RequireInvokableControllerRuleTest
 * @implements Rule<InClassNode>
 */
final class RequireInvokableControllerRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use invokable controller with __invoke() method instead of named action method';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! SymfonyControllerAnalyzer::isControllerScope($scope)) {
            return [];
        }

        $ruleErrors = [];

        $classLike = $node->getOriginalNode();
        foreach ($classLike->getMethods() as $classMethod) {
            if (! SymfonyControllerAnalyzer::isControllerActionMethod($classMethod)) {
                continue;
            }

            if ($classMethod->isMagic()) {
                continue;
            }

            if ($classMethod->name->toString() === MethodName::INVOKE) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(SymfonyRuleIdentifier::SYMFONY_REQUIRE_INVOKABLE_CONTROLLER)
                ->line($classMethod->getLine())
                ->build();
        }

        return $ruleErrors;
    }
}
