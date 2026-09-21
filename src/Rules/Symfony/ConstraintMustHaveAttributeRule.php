<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use Attribute;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\Validator\Constraint;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * Every class that extends Symfony Constraint must declare the #[\Attribute] attribute,
 * so it can be used as a PHP attribute on entity properties.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\ConstraintMustHaveAttributeRule\ConstraintMustHaveAttributeRuleTest
 *
 * @implements Rule<Class_>
 */
final readonly class ConstraintMustHaveAttributeRule implements Rule
{
    public const string ERROR_MESSAGE = 'Class "%s" extends Constraint but is missing the #[\Attribute] attribute. Add it, so the constraint can be used as an attribute on properties, as Symfony convention';

    private const string CONSTRAINT_CLASS = Constraint::class;

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     *
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // an anonymous class carries no name to report, and cannot be used as an attribute
        if (! $node->name instanceof Identifier) {
            return [];
        }

        // abstract base constraints are never used directly
        if ($node->isAbstract()) {
            return [];
        }

        $className = (string) $node->namespacedName;
        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        if (! $this->reflectionProvider->getClass($className)->is(self::CONSTRAINT_CLASS)) {
            return [];
        }

        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === Attribute::class) {
                    return [];
                }
            }
        }

        $identifierRuleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $className))
            ->identifier(RuleIdentifier::CONSTRAINT_HAS_ATTRIBUTE)
            ->build();

        return [$identifierRuleError];
    }
}
