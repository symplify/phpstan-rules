<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireArrayShapeReturnRule\Fixture;

final class ReportPositionalArrayReturn
{
    public function run(): array
    {
        return ['Tom', 30];
    }
}
