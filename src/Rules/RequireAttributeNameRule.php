<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Attribute;
use PhpParser\Node;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireAttributeNameRule\RequireAttributeNameRuleTest
 * @implements Rule<AttributeGroup>
 */
final class RequireAttributeNameRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Attribute must have all names explicitly defined';

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
            if (strncmp($attributeName, 'PHPUnit\Framework\Attributes\\', strlen('PHPUnit\Framework\Attributes\\')) === 0) {
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
}
