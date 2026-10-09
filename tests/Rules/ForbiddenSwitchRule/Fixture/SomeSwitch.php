<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenSwitchRule\Fixture;

final class SomeSwitch
{
    public function run(int $value): string
    {
        switch ($value) {
            case 1:
                return 'one';
            default:
                return 'many';
        }
    }
}
