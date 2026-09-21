<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture;

use Throwable;

final class SkipNullableException
{
    public function __construct(
        private readonly ?Throwable $previous,
    ) {
    }
}
