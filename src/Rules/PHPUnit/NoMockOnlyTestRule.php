<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\PHPUnit;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\ClassName;
use Symplify\PHPStanRules\Enum\PHPUnitRuleIdentifier;
use Symplify\PHPStanRules\Testing\PHPUnitTestAnalyser;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockOnlyTestRule\NoMockOnlyTestRuleTest
 *
 * @implements Rule<InClassNode>
 */
final class NoMockOnlyTestRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Test should have at least one non-mocked property, to test something';

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! PHPUnitTestAnalyser::isTestClass($scope)) {
            return [];
        }

        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        if ($classLike->getProperties() === []) {
            return [];
        }

        $hasExclusivelyMockedProperties = true;
        $hasSomeProperties = false;

        foreach ($classLike->getProperties() as $property) {
            if (! $property->type instanceof Name) {
                continue;
            }

            $propertyClassName = $property->type->toString();

            if ($propertyClassName !== ClassName::MOCK_OBJECT_CLASS) {
                $hasExclusivelyMockedProperties = false;
            } else {
                $hasSomeProperties = true;
            }
        }

        if ($hasExclusivelyMockedProperties === false || $hasSomeProperties === false) {
            return [];
        }

        $identifierRuleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->identifier(PHPUnitRuleIdentifier::NO_MOCK_ONLY)
            ->build();

        return [$identifierRuleError];
    }
}
