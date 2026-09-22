<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Attribute;
use PhpParser\Node;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireAttributeNameRule\RequireAttributeNameRuleTest
 * @implements Rule<AttributeGroup>
 */
final readonly class RequireAttributeNameRule implements Rule
{
    public const string ERROR_MESSAGE = 'Attribute must have all names explicitly defined';

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
    }

    public function getNodeType(): string
    {
        return AttributeGroup::class;
    }

    /**
     * @param AttributeGroup $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $ruleErrors = [];

        foreach ($node->attrs as $attribute) {
            $attributeName = $attribute->name->toString();
            if ($attributeName === Attribute::class) {
                continue;
            }

            // skip PHPUnit
            if (str_starts_with($attributeName, 'PHPUnit\Framework\Attributes\\')) {
                continue;
            }

            // single-param attribute is unambiguous, positional value is enough
            if ($this->hasSingleConstructorParam($attributeName)) {
                continue;
            }

            foreach ($attribute->args as $arg) {
                if ($arg->name instanceof Identifier) {
                    continue;
                }

                $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                    ->identifier(RuleIdentifier::REQUIRE_ATTRIBUTE_NAME)
                    ->line($attribute->getLine())
                    ->build();
            }
        }

        return $ruleErrors;
    }

    private function hasSingleConstructorParam(string $attributeName): bool
    {
        if (! $this->reflectionProvider->hasClass($attributeName)) {
            return false;
        }

        $classReflection = $this->reflectionProvider->getClass($attributeName);
        if (! $classReflection->hasConstructor()) {
            return false;
        }

        $extendedMethodReflection = $classReflection->getConstructor();
        foreach ($extendedMethodReflection->getVariants() as $extendedParametersAcceptor) {
            return count($extendedParametersAcceptor->getParameters()) === 1;
        }

        return false;
    }
}
