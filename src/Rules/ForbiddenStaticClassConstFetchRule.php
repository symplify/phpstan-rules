<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<ClassConstFetch>
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenStaticClassConstFetchRule\ForbiddenStaticClassConstFetchRuleTest
 */
final class ForbiddenStaticClassConstFetchRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Avoid static access of constants, as they can change value. Use interface and contract method instead';

    public function getNodeType(): string
    {
        return ClassConstFetch::class;
    }

    /**
     * @param ClassConstFetch $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->class instanceof Name) {
            return [];
        }

        if ($node->class->toString() !== 'static') {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::FORBIDDEN_STATIC_CLASS_CONST_FETCH)
            ->build()];
    }
}
