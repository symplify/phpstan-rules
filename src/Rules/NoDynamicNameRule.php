<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\TypeAnalyzer\CallableTypeAnalyzer;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\NoDynamicNameRuleTest
 *
 * @implements Rule<Node>
 */
final class NoDynamicNameRule implements Rule
{
    /**
     * @readonly
     */
    private CallableTypeAnalyzer $callableTypeAnalyzer;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit names over dynamic ones';

    public function __construct(CallableTypeAnalyzer $callableTypeAnalyzer)
    {
        $this->callableTypeAnalyzer = $callableTypeAnalyzer;
    }

    public function getNodeType(): string
    {
        // trick to allow multiple node types
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node instanceof ClassConstFetch || $node instanceof StaticPropertyFetch) {
            if (! $node->class instanceof Expr) {
                return [];
            }

            if (! $node->name instanceof Identifier) {
                return [];
            }

            if ($node->name->toString() === 'class') {
                return [];
            }

            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(RuleIdentifier::NO_DYNAMIC_NAME)
                ->build();

            return [$ruleError];
        }

        if ($node instanceof MethodCall || $node instanceof StaticCall || $node instanceof FuncCall || $node instanceof PropertyFetch) {

            if (! $node->name instanceof Expr) {
                return [];
            }

            if ($this->callableTypeAnalyzer->isClosureOrCallableType($scope, $node->name)) {
                return [];
            }

            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(RuleIdentifier::NO_DYNAMIC_NAME)
                ->build();

            return [$ruleError];
        }

        return [];
    }
}
