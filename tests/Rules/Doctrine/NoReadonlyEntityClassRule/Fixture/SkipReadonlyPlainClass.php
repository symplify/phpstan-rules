<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoReadonlyEntityClassRule\Fixture;

final readonly class SkipReadonlyPlainClass
{
    public function __construct(
        private string $name,
    ) {
    }
}
