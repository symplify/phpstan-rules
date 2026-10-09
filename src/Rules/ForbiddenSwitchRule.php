<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Switch_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Switch_>
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenSwitchRule\ForbiddenSwitchRuleTest
 */
final class ForbiddenSwitchRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'switch() is forbidden to use, use match() or early return instead';

    public function getNodeType(): string
    {
        return Switch_::class;
    }

    /**
     * @param Switch_ $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier('symplify.forbiddenSwitch')
            ->build()];
    }
}
