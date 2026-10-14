<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireArrayShapeReturnRule\Fixture;

final class SkipMixedKeyArrayReturn
{
    public function run(): array
    {
        return ['name' => 'Tom', 30];
    }
}
