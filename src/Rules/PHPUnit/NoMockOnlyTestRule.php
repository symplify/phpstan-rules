<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use TomasVotruba\Handyman\PHPStan\PHPUnitTestAnalyser;

/**
 * @see \TomasVotruba\Handyman\Tests\PHPStan\Rule\NoMockOnlyTestRule\NoMockOnlyTestRuleTest
 *
 * @implements Rule<InClassNode>
 */
final readonly class NoMockOnlyTestRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Test should have at least one non-mocked property, to test something';

    /**
     * @var string
     */
    private const MOCK_OBJECT_CLASS = 'PHPUnit\Framework\MockObject\MockObject';

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return RuleError[]
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

        foreach ($classLike->getProperties() as $property) {
            if (! $property->type instanceof Name) {
                continue;
            }

            $propertyClassName = $property->type->toString();

            if ($propertyClassName !== self::MOCK_OBJECT_CLASS) {
                $hasExclusivelyMockedProperties = false;
            }
        }

        if ($hasExclusivelyMockedProperties === false) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->build();
        return [$ruleError];
    }
}
