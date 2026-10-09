<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenSwitchRule\Fixture;

final class SkipMatch
{
    public function run(int $value): string
    {
        return match ($value) {
            1 => 'one',
            default => 'many',
        };
    }
}
