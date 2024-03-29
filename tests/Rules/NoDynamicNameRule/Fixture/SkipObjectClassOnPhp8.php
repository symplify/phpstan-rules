<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

use stdClass;

final class SkipObjectClassOnPhp8
{
    public function run(stdClass $stdClass): string
    {
        return $stdClass::class;
    }
}
