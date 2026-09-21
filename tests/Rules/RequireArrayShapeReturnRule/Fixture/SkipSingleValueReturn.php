<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireArrayShapeReturnRule\Fixture;

final class SkipSingleValueReturn
{
    public function run(): string
    {
        return 'value';
    }
}
