<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture;

use Closure;

final class SkipNullableClosure
{
    public function __construct(
        private readonly ?Closure $callback = null,
    ) {
    }
}
