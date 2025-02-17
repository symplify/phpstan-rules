<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoJustPropertyAssignRule\Fixture;

final class SkipScalarAssign
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function someMethod()
    {
        $name = $this->name;
    }
}
