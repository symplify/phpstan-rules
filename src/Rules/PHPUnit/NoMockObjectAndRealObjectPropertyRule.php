<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\PHPUnit;

use PhpParser\Node;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use Symplify\PHPStanRules\Enum\RuleIdentifier\PHPUnitRuleIdentifier;

/**
 * @implements Rule<Property>
 */
final class NoMockObjectAndRealObjectPropertyRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of ambiguous mock + object mix, pick single type that is more relevant';

    public function getNodeType(): string
    {
        return Property::class;
    }

    /**
     * @param Property $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->type instanceof IntersectionType && ! $node->type instanceof UnionType) {
            return [];
        }

        foreach ($node->type->types as $type) {
            if (! $type instanceof Name) {
                continue;
            }

            if ($type->toString() !== MockObject::class) {
                continue;
            }

            return [RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier(PHPUnitRuleIdentifier::NO_MOCK_OBJECT_AND_REAL_OBJECT_PROPERTY)
                ->build()];
        }

        return [];
    }
}
