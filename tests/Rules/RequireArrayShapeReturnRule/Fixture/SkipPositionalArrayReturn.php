<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireArrayShapeReturnRule\Fixture;

final class SkipPositionalArrayReturn
{
    public function run(): array
    {
        return ['Tom', 30];
    }
}
