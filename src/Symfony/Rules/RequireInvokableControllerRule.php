<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\ClassName;
use Symplify\PHPStanRules\Enum\MethodName;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyControllerAnalyzer;

/**
 * @see \Symplify\PHPStanRules\Tests\Symfony\Rules\RequireInvokableControllerRule\RequireInvokableControllerRuleTest
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
        $classReflection = $node->getClassReflection();
        if (
            ! $classReflection->isSubclassOf(ClassName::SYMFONY_ABSTRACT_CONTROLLER) &&
            ! $classReflection->isSubclassOf('Symfony\Bundle\FrameworkBundle\Controller\Controller')
        ) {
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
                ->identifier(RuleIdentifier::SYMFONY_REQUIRE_INVOKABLE_CONTROLLER)
                ->line($classMethod->getLine())
                ->build();
        }

        return $ruleErrors;
    }
}
