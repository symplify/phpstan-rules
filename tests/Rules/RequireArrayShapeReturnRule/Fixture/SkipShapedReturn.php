<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireArrayShapeReturnRule\Fixture;

final class SkipShapedReturn
{
    /**
     * @return array{name: string, age: int}
     */
    public function run(): array
    {
        return [
            'name' => 'Tom',
            'age' => 30,
        ];
    }
}
