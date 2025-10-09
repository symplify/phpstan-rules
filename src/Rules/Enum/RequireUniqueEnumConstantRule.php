<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Enum;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use Symplify\PHPStanRules\Enum\RuleIdentifier;
use Symplify\PHPStanRules\NodeAnalyzer\EnumAnalyzer;

/**
 * @implements Rule<InClassNode>
 * @see \Symplify\PHPStanRules\Tests\Rules\Enum\RequireUniqueEnumConstantRule\RequireUniqueEnumConstantRuleTest
 */
final class RequireUniqueEnumConstantRule implements Rule
{
    /**
     * @readonly
     */
    private EnumAnalyzer $enumAnalyzer;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Enum constants "%s" are duplicated. Make them unique instead';

    public function __construct(EnumAnalyzer $enumAnalyzer)
    {
        $this->enumAnalyzer = $enumAnalyzer;
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->enumAnalyzer->detect($scope, $node->getOriginalNode())) {
            return [];
        }

        $classLike = $node->getOriginalNode();
        $constantValues = $this->resolveClassConstantValues($classLike, $scope);
        if ($constantValues === []) {
            return [];
        }

        $duplicatedConstantValues = $this->filterDuplicatedValues($constantValues);
        if ($duplicatedConstantValues === []) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, implode('", "', $duplicatedConstantValues));

        $identifierRuleError = RuleErrorBuilder::message($errorMessage)
            ->identifier(RuleIdentifier::REQUIRE_UNIQUE_ENUM_CONSTANT)
            ->build();

        return [$identifierRuleError];
    }

    /**
     * @param array<int|float|bool|string> $values
     * @return array<int|float|bool|string>
     */
    private function filterDuplicatedValues(array $values): array
    {
        $countValues = array_count_values($values);

        $duplicatedValues = [];
        foreach ($countValues as $valueName => $valueCount) {
            if ($valueCount <= 1) {
                continue;
            }

            $duplicatedValues[] = $valueName;
        }

        return $duplicatedValues;
    }

    /**
     * @return array<int|float|bool|string>
     */
    private function resolveClassConstantValues(ClassLike $classLike, Scope $scope): array
    {
        $constantValues = [];
        foreach ($classLike->getConstants() as $constant) {
            foreach ($constant->consts as $const) {
                $constValueType = $scope->getType($const->value);
                if (! $constValueType instanceof ConstantStringType) {
                    continue;
                }

                $constantValues[] = $constValueType->getValue();
            }
        }

        return $constantValues;
    }
}
