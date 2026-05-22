<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<FuncCall>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\NoArrayMapWithArrayCallableRule\NoArrayMapWithArrayCallableRuleTest
 */
final class NoArrayMapWithArrayCallableRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid using array callables in array_map(), as it cripples static analysis on used method';

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @param FuncCall $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Name) {
            return [];
        }

        $functionName = $node->name->toString();
        if ($functionName !== 'array_map') {
            return [];
        }

        if ($node->isFirstClassCallable()) {
            return [];
        }

        $args = $node->getArgs();
        $firstArgValue = $args[0]->value;
        if (! $firstArgValue instanceof Array_) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::NO_ARRAY_MAP_WITH_ARRAY_CALLABLE)
            ->build();

        return [$identifierRuleError];
    }
}
