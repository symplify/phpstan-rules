<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Spotter;

use PhpParser\Node;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\If_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\NodeAnalyzer\CacheIfAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\IfElseBranchAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\IfResemblingMatchAnalyzer;
use Symplify\PHPStanRules\ValueObject\Spotter\IfAndCondExpr;
use Symplify\PHPStanRules\ValueObject\Spotter\ReturnAndAssignBranchCounts;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://www.php.net/manual/en/control-structures.match.php
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\IfElseToMatchSpotterRuleTest
 */
final class IfElseToMatchSpotterRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'If/else construction can be replace with more robust match()';

    public function __construct(
        private readonly IfElseBranchAnalyzer $ifElseBranchAnalyzer,
        private readonly IfResemblingMatchAnalyzer $ifResemblingMatchAnalyzer,
        private readonly CacheIfAnalyzer $cacheIfAnalyzer,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return If_::class;
    }

    /**
     * @param If_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // always need else
        if (! $node->else instanceof Else_) {
            return [];
        }

        // at least 3 options in total, so we don't match simple if/else
        if ($node->elseifs === []) {
            return [];
        }

        $branches = $this->mergeIfElseBranches($node);

        $ifsAndConds = [];
        foreach ($branches as $branch) {
            // must be exactly single item
            if (count($branch->stmts) !== 1) {
                return [];
            }

            // the conditioned parameters must be the same
            if ($branch instanceof If_ || $branch instanceof ElseIf_) {
                $ifsAndConds[] = new IfAndCondExpr($branch->stmts[0], $branch->cond);

                continue;
            }

            $ifsAndConds[] = new IfAndCondExpr($branch->stmts[0], null);
        }

        if ($this->shouldSkipIfsAndConds($ifsAndConds, $node)) {
            return [];
        }

        $returnAndAssignBranchCounts = $this->ifElseBranchAnalyzer->resolveBranchTypesToCount($ifsAndConds);

        $branchCount = count($branches);

        if (! $this->isUnitedMatchingBranchType($returnAndAssignBranchCounts, $branchCount)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function spot($value)
    {
        if ($value === 100) {
            $items = ['yes'];
        } else {
            $items = ['no'];
        }

        return $items;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function spot($value)
    {
        return match($value) {
            100 => ['yes'],
            default => ['no'],
        };
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isUnitedMatchingBranchType(
        ReturnAndAssignBranchCounts $returnAndAssignBranchCounts,
        int $branchCount
    ): bool {
        if ($returnAndAssignBranchCounts->getAssignTypeCount() === $branchCount) {
            return true;
        }

        return $returnAndAssignBranchCounts->getReturnTypeCount() === $branchCount;
    }

    /**
     * @return array<If_|Else_|ElseIf_>
     */
    private function mergeIfElseBranches(If_ $if): array
    {
        // all branches must have return or assign - at the same time

        /** @var array<If_|Else_|ElseIf_> $branches */
        $branches = array_merge([$if], $if->elseifs);
        if ($if->else instanceof Else_) {
            $branches[] = $if->else;
        }

        return $branches;
    }

    /**
     * @param IfAndCondExpr[] $ifsAndConds
     */
    private function shouldSkipIfsAndConds(array $ifsAndConds, If_ $if): bool
    {
        if (! $this->ifResemblingMatchAnalyzer->isUniqueCompareBinaryConds($ifsAndConds)) {
            return true;
        }

        return $this->cacheIfAnalyzer->isDefaultNullAssign($if);
    }
}
