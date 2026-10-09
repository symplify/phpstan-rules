<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<Trait_>
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenTraitRule\ForbiddenTraitRuleTest
 */
final class ForbiddenTraitRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Trait is forbidden to use, use explicit service composition instead';

    public function getNodeType(): string
    {
        return Trait_::class;
    }

    /**
     * @param Trait_ $node
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::FORBIDDEN_TRAIT)
            ->build()];
    }
}
