<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use Rector\Rector\AbstractRector;
use Symplify\PHPStanRules\Enum\RuleIdentifier\RectorRuleIdentifier;
use Symplify\PHPStanRules\Helper\NamingHelper;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\AvoidFeatureSetAttributeInRectorRule\AvoidFeatureSetAttributeInRectorRuleTest
 *
 * @implements Rule<InClassNode>
 */
final class AvoidFeatureSetAttributeInRectorRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of using Rector rule to setAttribute("%s") to be used later, create a service extending "DecoratingNodeVisitorInterface". This ensures attribute decoration and node changes are in 2 separated steps.';

    /**
     * @var string[]
     */
    private const ALLOWED_ATTRIBUTES = [
        // php-parser keys
        'kind', 'origNode', 'comments', 'startLine', 'endLine', 'startTokenPos', 'endTokenPos', 'rawValue', 'docLabel',
        // rector internal keys, designed to be changed and used by printer
        'wrapped_in_parentheses',
        'is_regular_pattern',
        'newlined_array_print',
    ];

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->is(AbstractRector::class)) {
            return [];
        }

        $classLike = $node->getOriginalNode();

        $nodeFinder = new NodeFinder();

        /** @var MethodCall[] $methodCalls */
        $methodCalls = $nodeFinder->findInstanceOf($classLike, MethodCall::class);

        $ruleErrors = [];

        foreach ($methodCalls as $methodCall) {
            if (! NamingHelper::isName($methodCall->name, 'setAttribute')) {
                continue;
            }

            $attributeName = $this->resolveAttributeKeyValue($methodCall, $scope);
            if (! is_string($attributeName)) {
                continue;
            }

            if (in_array($attributeName, self::ALLOWED_ATTRIBUTES, true)) {
                continue;
            }

            $ruleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $attributeName))
                ->identifier(RectorRuleIdentifier::AVOID_FEATURE_SET_ATTRIBUTE_IN_RECTOR)
                ->build();

            $ruleErrors[] = $ruleError;
        }

        return $ruleErrors;
    }

    private function resolveAttributeKeyValue(MethodCall $methodCall, Scope $scope): ?string
    {
        $firstArg = $methodCall->getArgs()[0];
        $attributeNameType = $scope->getType($firstArg->value);

        if (! $attributeNameType instanceof ConstantStringType) {
            return null;
        }

        return $attributeNameType->getValue();
    }
}
