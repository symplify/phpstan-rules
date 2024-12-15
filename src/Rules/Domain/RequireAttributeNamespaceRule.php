<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Domain;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<InClassNode>
 * @see \Symplify\PHPStanRules\Tests\Rules\Domain\RequireAttributeNamespaceRule\RequireAttributeNamespaceRuleTest
 */
final class RequireAttributeNamespaceRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Attribute must be located in "Attribute" namespace';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        if (! $classReflection->isAttributeClass()) {
            return [];
        }

        // is class in "Attribute" namespace?
        $className = $classReflection->getName();
        if (str_contains($className, '\\Attribute\\')) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)->build()];
    }
}
