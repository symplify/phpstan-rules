<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\RuleIdentifier;

/**
 * @implements Rule<Class_>
 */
final class NoEntityOutsideEntityNamespaceRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class with #[Entity] attribute must be located in "Entity" namespace to be loaded by Doctrine';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->hasEntityAttribute($node)) {
            return [];
        }

        // we need a namespace to check
        if (! $node->namespacedName instanceof Name) {
            return [];
        }

        $namespaceParts = $node->namespacedName->getParts();

        if (in_array('Entity', $namespaceParts, true)) {
            return [];
        }

        return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(RuleIdentifier::NO_ENTITY_OUTSIDE_ENTITY_NAMESPACE)
            ->build()];
    }

    private function hasEntityAttribute(Class_ $class): bool
    {
        foreach ($class->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === 'Doctrine\ORM\Mapping\Entity') {
                    return true;
                }

                if ($attr->name->toString() === 'Doctrine\ORM\Mapping\Embeddable') {
                    return true;
                }
            }
        }

        return false;
    }
}
