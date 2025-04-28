<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier\SymfonyRuleIdentifier;
use Symplify\PHPStanRules\Enum\SensioClass;
use Symplify\PHPStanRules\Enum\SymfonyClass;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\NoBareAndSecurityIsGrantedContentsRule\NoBareAndSecurityIsGrantedContentsRuleTest
 *
 * @implements Rule<Attribute>
 */
final class NoBareAndSecurityIsGrantedContentsRule implements Rule
{
    public const ERROR_MESSAGE = 'Instead of using one long "and" condition join, split into multiple standalone #[IsGranted] attributes';

    public function getNodeType(): string
    {
        return Attribute::class;
    }

    /**
     * @param Attribute $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! in_array($node->name->toString(), [SensioClass::SECURITY, SensioClass::IS_GRANTED, SymfonyClass::IS_GRANTED], true)) {
            return [];
        }

        $attributeExpr = $node->args[0]->value;
        if (! $attributeExpr instanceof String_) {
            return [];
        }

        // nothing to split
        if (strpos($attributeExpr->value, ' or ') !== false) {
            return [];
        }

        if (strpos($attributeExpr->value, ' and ') === false && strpos($attributeExpr->value, ' && ') === false) {
            return [];
        }

        if ($this->usesCustomFunctios($attributeExpr)) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(SymfonyRuleIdentifier::REQUIRED_IS_GRANTED_ENUM)
            ->build();

        return [$identifierRuleError];
    }

    private function usesCustomFunctios(String_ $string): bool
    {
        $joinedItems = preg_split('# (and|&&|or) #', $string->value, -1, PREG_SPLIT_NO_EMPTY);

        if ($joinedItems === false) {
            return false;
        }

        foreach ($joinedItems as $joinedItem) {
            if (strpos($joinedItem, 'is_granted') !== false) {
                continue;
            }

            if (strpos($joinedItem, 'has_role') !== false) {
                continue;
            }

            return true;
        }

        return false;
    }
}
