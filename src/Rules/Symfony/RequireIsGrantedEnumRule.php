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
 * @see \Symplify\PHPStanRules\Tests\Rules\Symfony\RequireIsGrantedEnumRule\RequireIsGrantedEnumRuleTest
 *
 * @implements Rule<Attribute>
 */
final class RequireIsGrantedEnumRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of "%s" string, use enum constant for #[IsGranted]';

    public function getNodeType(): string
    {
        return Attribute::class;
    }

    /**
     * @param Attribute $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! in_array($node->name->toString(), [SensioClass::IS_GRANTED, SymfonyClass::IS_GRANTED], true)) {
            return [];
        }

        $isGrantedExpr = $node->args[0]->value;
        if (! $isGrantedExpr instanceof String_) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $isGrantedExpr->value))
            ->identifier(SymfonyRuleIdentifier::REQUIRED_IS_GRANTED_ENUM)
            ->build();

        return [$identifierRuleError];
    }
}
