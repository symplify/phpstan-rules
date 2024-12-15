<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Const_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Const_>
 * @see \Symplify\PHPStanRules\Tests\Rules\NoGlobalConstRule\NoGlobalConstRuleTest
 */
final class NoGlobalConstRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Global constants are forbidden. Use enum-like class list instead';

    public function getNodeType(): string
    {
        return Const_::class;
    }

    /**
     * @param Const_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)->build()];
    }
}
