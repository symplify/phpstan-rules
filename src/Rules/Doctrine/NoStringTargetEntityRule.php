<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Doctrine;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\DoctrineRuleIdentifier;

/**
 * A Doctrine association attribute must reference its target entity as a class constant (Target::class), not a string.
 * A string skips IDE navigation, refactoring and static analysis, and hides typos until runtime.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Doctrine\NoStringTargetEntityRule\NoStringTargetEntityRuleTest
 *
 * @implements Rule<Attribute>
 */
final class NoStringTargetEntityRule implements Rule
{
    public const string ERROR_MESSAGE = 'Doctrine association #[%s] uses a string targetEntity "%s"; use %s::class instead';

    /**
     * @var string[]
     */
    private const array ASSOCIATION_ATTRIBUTES = [
        'Doctrine\ORM\Mapping\ManyToOne',
        'Doctrine\ORM\Mapping\OneToMany',
        'Doctrine\ORM\Mapping\OneToOne',
        'Doctrine\ORM\Mapping\ManyToMany',
    ];

    public function getNodeType(): string
    {
        return Attribute::class;
    }

    /**
     * @param Attribute $node
     *
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! in_array($node->name->toString(), self::ASSOCIATION_ATTRIBUTES, true)) {
            return [];
        }

        foreach ($node->args as $arg) {
            if (! $arg->name instanceof Identifier || $arg->name->toString() !== 'targetEntity') {
                continue;
            }

            if (! $arg->value instanceof String_) {
                return [];
            }

            return [
                RuleErrorBuilder::message(
                    sprintf(self::ERROR_MESSAGE, $node->name->getLast(), $arg->value->value, $arg->value->value)
                )
                    ->identifier(DoctrineRuleIdentifier::NO_STRING_TARGET_ENTITY)
                    ->build(),
            ];
        }

        return [];
    }
}
