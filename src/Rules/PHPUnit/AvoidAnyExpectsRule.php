<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\PHPUnit;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\PHPUnitRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;
use Symplify\PHPStanRules\PHPUnit\TestClassDetector;

/**
 * @implements Rule<MethodCall>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\PHPUnit\AvoidAnyExpectsRule\AvoidAnyExpectsRuleTest
 */
final class AvoidAnyExpectsRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Using $this->any() on mock is ambigous. Use explicit count or change to a stub';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! TestClassDetector::isTestClass($scope)) {
            return [];
        }

        if (! NamingHelper::isName($node->name, 'expects')) {
            return [];
        }

        $firstArg = $node->getArgs()[0];
        if (! $firstArg->value instanceof MethodCall) {
            return [];
        }

        $nestedCall = $firstArg->value;
        if (! NamingHelper::isName($nestedCall->name, 'any')) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(PHPUnitRuleIdentifier::AVOID_ANY_EXPECTS)
            ->build();

        return [$identifierRuleError];
    }
}
