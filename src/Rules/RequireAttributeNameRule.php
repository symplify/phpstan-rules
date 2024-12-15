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
            if (str_starts_with($attributeName, 'PHPUnit\Framework\Attributes\\')) {
                continue;
            }

            foreach ($attribute->args as $arg) {
                if ($arg->name instanceof Identifier) {
                    continue;
                }

                $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                    ->line($attribute->getLine())
                    ->build();
            }
        }

        return $ruleErrors;
    }
}
