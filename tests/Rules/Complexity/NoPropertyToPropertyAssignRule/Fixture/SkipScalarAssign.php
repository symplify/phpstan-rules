<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertyToPropertyAssignRule\Fixture;

final class SkipScalarAssign
{
    private string $body = '';

    private string $bodyInitial = '';

    public function run(): void
    {
        $this->bodyInitial = $this->body;
    }
}
