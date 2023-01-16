<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Enum;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Type\Constant\ConstantStringType;
use Symplify\PHPStanRules\NodeAnalyzer\EnumAnalyzer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Enum\RequireUniqueEnumConstantRule\RequireUniqueEnumConstantRuleTest
 */
final class RequireUniqueEnumConstantRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Enum constants "%s" are duplicated. Make them unique instead';

    public function __construct(
        private readonly EnumAnalyzer $enumAnalyzer
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return string[]
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
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use MyCLabs\Enum\Enum;

class SomeClass extends Enum
{
    private const YES = 'yes';

    private const NO = 'yes';
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use MyCLabs\Enum\Enum;

class SomeClass extends Enum
{
    private const YES = 'yes';

    private const NO = 'no';
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param array<string|int> $values
     * @return array<string|int>
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
        foreach ($classLike->getConstants() as $classConst) {
            foreach ($classConst->consts as $const) {
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
