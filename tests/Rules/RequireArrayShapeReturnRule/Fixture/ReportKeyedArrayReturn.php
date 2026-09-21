<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireArrayShapeReturnRule\Fixture;

final class ReportKeyedArrayReturn
{
    public function run(): array
    {
        return [
            'name' => 'Tom',
            'age' => 30,
        ];
    }
}
