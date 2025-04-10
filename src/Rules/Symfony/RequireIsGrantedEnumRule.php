<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Symfony;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

final class RequireIsGrantedEnumRule implements Rule
{
    public function getNodeType(): string
    {
        return Attribute::class;
    }

    /**
     * @param Attribute $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->name->toString() !== IsGranted::class) {
            return [];
        }

        $isGrantedExpr = $node->args[0]->value;
        if (! $isGrantedExpr instanceof String_) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf('Instead of "%s" string, use enum constant for #[IsGranted]', $isGrantedExpr->value)
            )
                ->identifier('symfony.requiredIsGrantedEnum')
                ->build(),
        ];
    }
}
