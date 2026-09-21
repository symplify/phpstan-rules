<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertyToPropertyAssignRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertyToPropertyAssignRule\Source\SomeService;

final class ReportPropertyAssign
{
    private SomeService $primary;

    private SomeService $secondary;

    public function run(): void
    {
        $this->primary = $this->secondary;
    }
}
