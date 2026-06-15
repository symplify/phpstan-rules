<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\PHPUnit;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
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
 * @see \Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoWithOnStubRule\NoWithOnStubRuleTest
 */
final class NoWithOnStubRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Using with() on a stub is misleading and deprecated by PHPUnit. Use explicit expects() to turn it into a mock, or drop with()';

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
        if (! NamingHelper::isName($node->name, 'with')) {
            return [];
        }

        if (! TestClassDetector::isTestClass($scope)) {
            return [];
        }

        if (! $node->var instanceof MethodCall) {
            return [];
        }

        $methodCall = $node->var;
        if (! NamingHelper::isName($methodCall->name, 'method')) {
            return [];
        }

        if ($methodCall->var instanceof MethodCall && NamingHelper::isName($methodCall->var->name, 'expects')) {
            return [];
        }

        if (! $methodCall->var instanceof Variable && ! $methodCall->var instanceof PropertyFetch) {
            return [];
        }

        $callerType = $scope->getType($methodCall->var);
        if (! $callerType->hasMethod('expects')->yes()) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(PHPUnitRuleIdentifier::NO_WITH_ON_STUB)
            ->build();

        return [$identifierRuleError];
    }
}
