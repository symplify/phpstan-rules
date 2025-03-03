<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\NodeAnalyzer\MethodCallNameAnalyzer;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyControllerAnalyzer;

/**
 * @implements Rule<MethodCall>
 */
final class NoGetDoctrineInControllerRule implements Rule
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Do not use $this->getDoctrine() method in controller. Use __construct(EntityManagerInterface $entityManager) instead';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! MethodCallNameAnalyzer::isThisMethodCall($node, 'getDoctrine')) {
            return [];
        }

        if (! SymfonyControllerAnalyzer::isControllerScope($scope)) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(SymfonyRuleIdentifier::NO_GET_DOCTRINE_IN_CONTROLLER)
            ->build();

        return [$identifierRuleError];
    }
}
