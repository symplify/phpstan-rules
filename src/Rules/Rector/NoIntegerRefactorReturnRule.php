<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Rector;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\RectorRuleIdentifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Rector\NoIntegerRefactorReturnRule\NoIntegerRefactorReturnRuleTest
 *
 * @implements Rule<ClassMethod>
 */
final class NoIntegerRefactorReturnRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of using int in refactor(), make use of attributes and return always node or a null. Using traverser enums might lead to unexpected results';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->isPublic()) {
            return [];
        }

        if ($node->name->toString() !== 'refactor') {
            return [];
        }

        if (! $node->returnType instanceof UnionType) {
            return [];
        }

        foreach ($node->returnType->types as $type) {
            if (! $type instanceof Identifier) {
                continue;
            }

            if ($type->name === 'int') {
                $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                    ->identifier(RectorRuleIdentifier::NO_INTEGER_REFACTOR_RETURN)
                    ->build();

                return [$ruleError];
            }
        }

        return [];
    }
}
