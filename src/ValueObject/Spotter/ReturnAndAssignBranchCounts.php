<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject\Spotter;

final class ReturnAndAssignBranchCounts
{
    public function __construct(
        private readonly int $returnTypeCount,
        private readonly int $assignTypeCount
    ) {
    }

    public function getReturnTypeCount(): int
    {
        return $this->returnTypeCount;
    }

    public function getAssignTypeCount(): int
    {
        return $this->assignTypeCount;
    }
}
